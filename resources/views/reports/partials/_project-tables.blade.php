@forelse($projects as $project => $records)
    <div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-chart-line text-primary me-1"></i>
            {{ $records->first()->project_name ?? $project }}
        </h5>
    </div>
        <div class="card-body p-2" style="font-size: 13px;">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Installs</th>
                            <th>Uninstalls</th>
                            <th>Uninstalls Rate</th>
                            <th>Users Total</th>
                            <th>Rating</th>
                            <th>Feedbacks</th>
                            <th>Overal Rank</th>
                            <th>Category Rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $report)
                            @php
                                $rateValue = floatval(str_replace('%', '', $report->uninstall_rate));
                                $rateClamped = max(0, min(100, $rateValue));
                                $hue = 120 - ($rateClamped * 1.2);
                                $progressColor = "hsl($hue, 80%, 50%)";
                            @endphp
                            <tr>
                                <td><strong>{{ $report->date }}</strong></td>
                                <td>{{ $report->installs }}</td>
                                <td>{{ $report->uninstalls }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 mr-2" style="height: 15px;">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: {{ $rateClamped }}%; background-color: {{ $progressColor }}"
                                                 aria-valuenow="{{ $rateClamped }}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="badge" style="background-color: {{ $progressColor }};">
                                            {{ number_format($rateClamped, 2) }}%
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $report->users_total ?? 0 }}</td>
                                <td>{{ $report->rating ?? 0 }}</td>
                                <td>
                                    {{ $report->feedbacks_total ?? 0 }}

                                    @if (isset($report->feedbacks_total_comparison))
                                        @php
                                            $diffText = trim(str_replace(['(', ')'], '', $report->feedbacks_total_comparison));
                                            $diff = (int) str_replace(['+', '-'], '', $diffText);
                                            $isIncreased = strpos($report->feedbacks_total_comparison, '+') !== false;
                                            $colorClass = $diff === 0 ? 'bg-secondary' : ($isIncreased ? 'bg-success' : 'bg-danger');
                                            $sign = $diff === 0 ? '0' : ($isIncreased ? '+' . $diff : '-' . $diff);
                                        @endphp

                                        <span class="badge {{ $colorClass }} ms-1">{{ $sign }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $report->overal_rank ?? 0 }}

                                    @if (isset($report->overal_rank_comparison))
                                        @php
                                            $diffText = trim(str_replace(['(', ')'], '', $report->overal_rank_comparison));
                                            $diff = (int) str_replace(['+', '-'], '', $diffText);
                                            $isImproved = strpos($report->overal_rank_comparison, '-') !== false;
                                            $colorClass = $diff === 0 ? 'bg-secondary' : ($isImproved ? 'bg-success' : 'bg-danger');
                                            $sign = $diff === 0 ? '0' : ($isImproved ? '+' . $diff : '-' . $diff);
                                        @endphp

                                        <span class="badge {{ $colorClass }} ms-1">{{ $sign }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $report->cat_rank ?? 0 }}

                                    @if (isset($report->cat_rank_comparison))
                                        @php
                                            $diffText = trim(str_replace(['(', ')'], '', $report->cat_rank_comparison));
                                            $diff = (int) str_replace(['+', '-'], '', $diffText);
                                            $isImproved = strpos($report->cat_rank_comparison, '-') !== false;
                                            $colorClass = $diff === 0 ? 'bg-secondary' : ($isImproved ? 'bg-success' : 'bg-danger');
                                            $sign = $diff === 0 ? '0' : ($isImproved ? '+' . $diff : '-' . $diff);
                                        @endphp

                                        <span class="badge {{ $colorClass }} ms-1">{{ $sign }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-warning">Немає звітів</div>
@endforelse
