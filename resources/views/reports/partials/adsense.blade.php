{{-- resources/views/reports/partials/adsense.blade.php --}}
<style>
.adsense-box {
    padding: 18px 12px 13px 18px;
    border-radius: 12px;
    background: #0d6efd;
    color: #fff;
    min-width: 180px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 18px;
}
.adsense-value {
    font-size: 2.25rem;
    font-weight: 700;
    line-height: 1.1;
}
.adsense-label {
    font-size: 1.05rem;
    font-weight: 400;
    opacity: 0.97;
    margin-bottom: 4px;
    margin-top: 2px;
}
.adsense-yesterday {
    font-size: 1rem;
    font-weight: 500;
    opacity: 0.92;
    margin-top: 8px;
    display: flex;
    align-items: center;
}
.adsense-yesterday i {
    margin-right: 7px;
    color: #fff8;
    font-size: 1em;
}
.adsense-y-label {
    color: #fff9;
    margin-right: 2px;
    font-size: 0.97em;
    font-weight: 400;
}
.adsense-y-value {
    color: #fff;
    font-weight: 600;
    font-size: 1.07em;
}
</style>

<div class="row">
    @php
        $fields = [
            'estimated_earnings' => 'Earnings',
            'clicks' => 'Clicks',
            'impressions' => 'Impressions',
            'page_views' => 'Page Views',
            'impressions_rpm' => 'RPM',
            'cost_per_click' => 'CPC',
        ];
    @endphp

@foreach ($fields as $field => $label)
    @php
        $isDecimal = in_array($field, ['estimated_earnings', 'impressions_rpm', 'cost_per_click']);
        $todayValue = $adsenseToday?->{$field} ?? 0;
        $yesterdayValue = $adsenseYesterday?->{$field} ?? 0;
    @endphp
    <div class="col-md-2">
        <div class="adsense-box">
            <div class="adsense-value">
                @if ($field === 'estimated_earnings')
                    <span style="font-size:1.1em; font-weight:500; margin-right:2px;">$</span>
                @endif
                {{ number_format($todayValue, $isDecimal ? 2 : 0, '.', ' ') }}
                {{-- Стрілочки для RPM та CPC --}}
                @if(in_array($field, ['impressions_rpm', 'cost_per_click']) && $adsenseYesterday)
                    @php
                        $icon = '';
                        $color = '';
                        if ($todayValue > $yesterdayValue) {
                            $icon = 'fa-arrow-up';
                            $color = 'text-success';
                        } elseif ($todayValue < $yesterdayValue) {
                            $icon = 'fa-arrow-down';
                            $color = 'text-danger';
                        }
                    @endphp
                    @if($icon)
                        <i class="fas {{ $icon }} {{ $color }} ms-1" style="font-size:1em"></i>
                    @endif
                @endif
            </div>
            <div class="adsense-label">{{ $label }}</div>
            @if($adsenseYesterday)
                <div class="adsense-yesterday">
                    <i class="fas fa-calendar-day"></i>
                    <span class="adsense-y-label">yesterday:</span>
                    <span class="adsense-y-value">
                        @if ($field === 'estimated_earnings')
                            <span style="font-size:1em; font-weight:500; margin-right:2px;">$</span>
                        @endif
                        {{ number_format($yesterdayValue, $isDecimal ? 2 : 0, '.', ' ') }}
                    </span>
                </div>
            @endif
        </div>
    </div>
@endforeach

</div>
