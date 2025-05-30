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
                            <th>Extension install</th>
                            <th>Extension active</th>
                            <th>Extension update</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records->slice(0, -1) as $report)
                            @php
                                $rateValue = floatval(str_replace('%', '', $report->uninstall_rate ?? '0%'));
                                $rateClamped = max(0, min(100, $rateValue));
                                $hue = 120 - ($rateClamped * 1.2);
                                $progressColor = "hsl($hue, 80%, 50%)";

                                // Reusable function to render badge
                                $renderBadge = function ($value, $comparison, $isFloat = false, $isRank = false) {
                                    if (isset($comparison) && $comparison !== '' && !empty($value) && $value != 0) {
                                        $diffText = trim(str_replace(['(', ')'], '', $comparison));
                                        $diff = $isFloat ? (float) str_replace(['+', '-'], '', $diffText) : (int) str_replace(['+', '-'], '', $diffText);
                                        $isPositive = strpos($comparison, '+') !== false;
                                        // For ranks, a decrease (negative diff) is an improvement (green); increase (positive diff) is regression (red)
                                        $isImproved = $isRank ? !$isPositive : $isPositive;
                                        $colorClass = $diff !== 0 ? ($isImproved ? 'bg-success' : 'bg-danger') : '';
                                        // For ranks, reverse the sign display: green (+) for negative diff, red (-) for positive diff
                                        $sign = $diff !== 0 ? ($isRank ? ($isPositive ? '-' . $diff : '+' . $diff) : ($isPositive ? '+' . $diff : '-' . $diff)) : '';
                                        return $diff !== 0 ? '<span class="badge ' . $colorClass . ' ms-1">' . $sign . '</span>' : '';
                                    }
                                    return '';
                                };
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
                                                aria-valuenow="{{ $rateClamped }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="badge" style="background-color: {{ $progressColor }};">
                                            {{ number_format($rateClamped, 2) }}%
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $report->users_total ?? 0 }}</td>
                                <td>
                                    {{ $report->rating_value ?? 0 }}
                                    {!! $renderBadge($report->rating_value, $report->rating_value_comparison ?? '', true, false) !!}
                                </td>
                                <td>
                                    {{ $report->feedbacks_total ?? 0 }}
                                    {!! $renderBadge($report->feedbacks_total, $report->feedbacks_total_comparison ?? '', false, false) !!}
                                </td>
                                <td>
                                    {{ $report->overal_rank ?? 0 }}
                                    {!! $renderBadge($report->overal_rank, $report->overal_rank_comparison ?? '', false, true) !!}
                                </td>
                                <td>
                                    {{ $report->cat_rank ?? 0 }}
                                    {!! $renderBadge($report->cat_rank, $report->cat_rank_comparison ?? '', false, true) !!}
                                </td>

                                <td>
                                    {{ $report->extension_install ?? 0 }}
                                    {!! $renderBadge($report->extension_install, $report->extension_install_comparison ?? '', false, false) !!}
                                </td>

                                <td>
                                    {{ $report->extension_active ?? 0 }}
                                    {!! $renderBadge($report->extension_active, $report->extension_active_comparison ?? '', false, false) !!}
                                </td>
                                
                                <td>
                                    {{ $report->extension_update ?? 0 }}
                                    {!! $renderBadge($report->extension_update, $report->extension_update_comparison ?? '', false, false) !!}
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