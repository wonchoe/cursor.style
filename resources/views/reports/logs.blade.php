@extends('adminlte::page')

@section('title', 'Laravel Logs')
@section('content_header')
    <h1>Laravel Logs</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <form id="fileForm" class="d-inline-block mr-2">
            <select name="file" id="logFile" class="form-control">
                @foreach($files as $file)
                    <option value="{{ $file }}" {{ $file == $selected ? 'selected' : '' }}>{{ $file }}</option>
                @endforeach
            </select>
        </form>
        <form id="levelForm" class="d-inline-block mr-2">
            <select name="level" id="logLevel" class="form-control">
                <option value="">Всі рівні</option>
                <option value="info">ℹ️ Info</option>
                <option value="warning">⚠️ Warning</option>
                <option value="error">❌ Error</option>
                <option value="debug">🐞 Debug</option>
                <option value="other">Інше</option>
            </select>
        </form>
        <button id="clearLogs" class="btn btn-danger ml-auto" style="margin-left:auto;"><i class="fas fa-trash"></i> Очистити лог</button>
    </div>
    <div id="logsTable" class="table-responsive" style="max-height: 75vh; overflow-y:auto;">
        <table class="table table-sm table-hover" style="font-size:14px;">
            <tbody id="logBody"></tbody>
        </table>
    </div>
    <div id="logsPaginator" class="mt-3"></div>
</div>
@endsection

@push('js')
<style>
.json-view { background: #23272f; color: #fff; padding: 10px; border-radius: 6px; font-size: 13px; margin-top: 10px;}
.json-key { color: #66d9ef; }
.json-string { color: #a5e075; }
.json-number { color: #ffd700; }
.json-boolean { color: #ff80bf; }
.json-null { color: #d4bfff; }
.log-row-error { background: #ff000012 !important; }
.log-row-warning { background: #fff70016 !important; }
</style>
<script>
let shownLogHashes = [];
let currentFile = null;
let currentLevel = '';
let currentPage = 1;
let totalLogs = 0;
let perPage = 100;

function escapeHtml(text) {
    return $('<div/>').text(text).html();
}

function parseLevel(line) {
    if (line.includes('ERROR'))   return {icon:'fa-exclamation-triangle text-danger', lvl:'error'};
    if (line.includes('WARNING')) return {icon:'fa-exclamation-circle text-warning',  lvl:'warning'};
    if (line.includes('INFO'))    return {icon:'fa-info-circle text-info',            lvl:'info'};
    if (line.includes('DEBUG'))   return {icon:'fa-bug text-secondary',               lvl:'debug'};
    return {icon:'fa-dot-circle text-muted', lvl:'other'};
}

function logHash(log) {
    return log.length + '-' + log.slice(0, 80);
}

function highlightJson(json) {
    if (typeof json != 'string') {
        json = JSON.stringify(json, undefined, 2);
    }
    json = escapeHtml(json);
    json = json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(\.\d*)?([eE][+\-]?\d+)?)/g, function (match) {
        let cls = 'json-number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) cls = 'json-key';
            else cls = 'json-string';
        } else if (/true|false/.test(match)) {
            cls = 'json-boolean';
        } else if (/null/.test(match)) {
            cls = 'json-null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
    return `<pre class="json-view">${json}</pre>`;
}

// --- Пре-пендить нові логи (лайвтрейл)
function renderNewLogs(lines) {
    let newHtml = '';
    lines.forEach((log, idx) => {
        let hash = logHash(log);
        if (shownLogHashes.includes(hash)) return;

        let logLines = log.split('\n');
        let summary = escapeHtml(logLines[0]);
        let rest = logLines.slice(1).join('\n');

        let jsonPretty = '';
        let restRaw = escapeHtml(rest);
        let jsonMatch = rest.match(/(\{.*\}|\[.*\])$/s);
        if (jsonMatch) {
            try {
                let obj = JSON.parse(jsonMatch[0]);
                jsonPretty = highlightJson(obj);
                restRaw = escapeHtml(rest.replace(jsonMatch[0], '')).trim();
            } catch(e) {}
        }

        const {icon, lvl} = parseLevel(log);
        let hasDetails = logLines.length > 1 || jsonPretty;
        let expandBtn = hasDetails
            ? `<button class="btn btn-link btn-sm px-1 py-0 toggle-expand" data-idx="${shownLogHashes.length}" style="font-size:13px;">[+]</button>`
            : '';

        let trClass = '';
        if (lvl === 'error')   trClass = 'log-row-error';
        if (lvl === 'warning') trClass = 'log-row-warning';

        newHtml += `<tr class="${trClass}">
            <td style="width:38px;vertical-align:top;"><i class="fas ${icon}"></i></td>
            <td>
                <span class="log-short" id="short-${shownLogHashes.length}">
                    ${summary}
                    ${expandBtn}
                </span>
                <div class="log-full d-none" id="full-${shownLogHashes.length}" style="white-space:pre-line; background:#222; color:#fff; padding:10px; border-radius:7px; margin-top:8px;">
                    <span style="font-weight:600">${summary}</span>
                    ${restRaw ? '<br>' + restRaw : ''}
                    ${jsonPretty}
                </div>
            </td>
        </tr>`;

        shownLogHashes.push(hash);
    });
    if (newHtml) $('#logBody').prepend(newHtml);
}

function renderAllLogs(lines) {
    let html = '';
    shownLogHashes = [];
    lines.forEach((log, idx) => {
        let hash = logHash(log);
        let logLines = log.split('\n');
        let summary = escapeHtml(logLines[0]);
        let rest = logLines.slice(1).join('\n');

        let jsonPretty = '';
        let restRaw = escapeHtml(rest);
        let jsonMatch = rest.match(/(\{.*\}|\[.*\])$/s);
        if (jsonMatch) {
            try {
                let obj = JSON.parse(jsonMatch[0]);
                jsonPretty = highlightJson(obj);
                restRaw = escapeHtml(rest.replace(jsonMatch[0], '')).trim();
            } catch(e) {}
        }

        const {icon, lvl} = parseLevel(log);
        let hasDetails = logLines.length > 1 || jsonPretty;
        let expandBtn = hasDetails
            ? `<button class="btn btn-link btn-sm px-1 py-0 toggle-expand" data-idx="${idx}" style="font-size:13px;">[+]</button>`
            : '';

        let trClass = '';
        if (lvl === 'error')   trClass = 'log-row-error';
        if (lvl === 'warning') trClass = 'log-row-warning';

        html += `<tr class="${trClass}">
            <td style="width:38px;vertical-align:top;"><i class="fas ${icon}"></i></td>
            <td>
                <span class="log-short" id="short-${idx}">
                    ${summary}
                    ${expandBtn}
                </span>
                <div class="log-full d-none" id="full-${idx}" style="white-space:pre-line; background:#222; color:#fff; padding:10px; border-radius:7px; margin-top:8px;">
                    <span style="font-weight:600">${summary}</span>
                    ${restRaw ? '<br>' + restRaw : ''}
                    ${jsonPretty}
                </div>
            </td>
        </tr>`;

        shownLogHashes.push(hash);
    });
    if (html) {
        $('#logBody').html(html);
    } else {
        $('#logBody').html(`<tr><td colspan="2" class="text-center text-muted" style="padding:40px;">No logs found for this filter.</td></tr>`);
    }
}

function clearLogList() {
    $('#logBody').empty();
    shownLogHashes = [];
}

function pollLogs() {
    if (currentPage !== 1) return; // тільки на першій сторінці
    fetchLogs(true);
}

function fetchLogs(liveTail = false) {
    const selectedFile = $('#logFile').val();
    const selectedLevel = $('#logLevel').val();
    // Якщо файл чи рівень логів змінився — збиваємо
    if (currentFile !== selectedFile || currentLevel !== selectedLevel) {
        clearLogList();
        currentFile = selectedFile;
        currentLevel = selectedLevel;
        currentPage = 1;
    }
    // Зберігаємо у localStorage
    localStorage.setItem('log_file', selectedFile);
    localStorage.setItem('log_level', selectedLevel);

    $.get('{{ route('admin.logs.fetch') }}', {
        file: selectedFile,
        page: currentPage,
        perPage: perPage,
        level: selectedLevel
    }, function(resp){
        totalLogs = resp.total;
        if (liveTail) {
            if (resp.lines.length) {
                renderNewLogs(resp.lines);
            }
        } else {
            renderAllLogs(resp.lines);
        }
        renderPaginator();
    });
}

function renderPaginator() {
    let totalPages = Math.ceil(totalLogs / perPage);
    if (totalPages <= 1) {
        $('#logsPaginator').html('');
        return;
    }
    let html = '<nav><ul class="pagination pagination-sm justify-content-center">';
    for (let i = 1; i <= totalPages; i++) {
        if (i === currentPage) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 2) {
            html += `<li class="page-item"><a class="page-link page-num" href="#" data-page="${i}">${i}</a></li>`;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }
    }
    html += '</ul></nav>';
    $('#logsPaginator').html(html);
}

$(function(){
    // Встановити log_file/log_level з localStorage
    let savedFile = localStorage.getItem('log_file');
    let savedLevel = localStorage.getItem('log_level');
    if (savedFile) $('#logFile').val(savedFile);
    if (savedLevel) $('#logLevel').val(savedLevel);

    fetchLogs();

    setInterval(pollLogs, 3500);

    $('#logFile, #logLevel').on('change', function(){
        currentPage = 1;
        fetchLogs();
    });

    $('#clearLogs').on('click', function(e){
        e.preventDefault();
        if (confirm('Очистити цей log-файл?')) {
            $.post('{{ route('admin.logs.clear') }}', {_token:'{{ csrf_token() }}', file: $('#logFile').val()}, function(){
                clearLogList();
                currentPage = 1;
                fetchLogs();
            });
        }
    });

    $(document).on('click', '.toggle-expand', function(){
        const idx = $(this).data('idx');
        $(`#short-${idx}`).toggleClass('d-none');
        $(`#full-${idx}`).toggleClass('d-none');
        $(this).text($(this).text() == '[+]' ? '[-]' : '[+]');
    });

    $(document).on('click', '.page-num', function(e){
        e.preventDefault();
        currentPage = parseInt($(this).data('page'));
        fetchLogs();
    });
});
</script>
@endpush
