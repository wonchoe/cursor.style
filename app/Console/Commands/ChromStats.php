<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reports;
use Carbon\Carbon;

class ChromStats extends Command
{

    protected $signature = 'custom:ChromStats';
    protected $description = 'Get data from Chrome stat';

public function parseChromeStats($html)
{
    $result = [
        'users_total'    => null,
        'rating_value'   => null,
        'feedbacks_total'=> null,
        'overal_rank'    => null,
        'cat_name'       => null,
        'cat_rank'       => null,
    ];

    // Users
    preg_match('/<td class="table-cell-3qU2Lo">Users<\/td>\s*<td class="table-cell-3qU2Lo">([^<]+)<\/td>/', $html, $m);
    $result['users_total'] = isset($m[1]) ? (int) str_replace([',', '+', ' '], '', $m[1]) : null;

    // Rating value
    preg_match('/<td class="table-cell-3qU2Lo">Average rating<\/td>\s*<td class="table-cell-3qU2Lo">([^<]+)<\/td>/', $html, $m);
    $result['rating_value'] = isset($m[1]) ? trim($m[1]) : null;

    // Feedbacks total
    preg_match('/<td class="table-cell-3qU2Lo">Rating count<\/td>\s*<td class="table-cell-3qU2Lo">([^<]+)<\/td>/', $html, $m);
    $result['feedbacks_total'] = isset($m[1]) ? (int) str_replace([',', ' '], '', $m[1]) : null;

    // Overal rank
    if (preg_match('/<tr[^>]+id="overall-rank"[^>]*>(.*?)<\/tr>/si', $html, $m)) {
        $tr = $m[1];
        if (preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $tr, $cells) && count($cells[1]) >= 2) {
            $result['overal_rank'] = (int) preg_replace('/\D/', '', strip_tags($cells[1][1]));
        }
    }

    // Категорія: перший <tr> після overall-rank з id="cat-*"
    $catRow = null;
    if (preg_match('/<tr[^>]+id="overall-rank"[^>]*>.*?<\/tr>(.*?)(<tr[^>]+id="cat-[^"]*-rank"[^>]*>.*?<\/tr>)/si', $html, $m)) {
        $catRowHtml = $m[2];
        if (preg_match('/<tr[^>]+id="cat-([^"]*)-rank"[^>]*>(.*?)<\/tr>/si', $catRowHtml, $trMatch)) {
            $cat_tr = $trMatch[2];
            if (preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $cat_tr, $cells) && count($cells[1]) >= 2) {
                // Назва категорії
                if (preg_match('/<a[^>]*>([^<]+)<\/a>/', $cells[1][0], $catNameMatch)) {
                    $result['cat_name'] = trim($catNameMatch[1]);
                }
                // Ранк по категорії
                $result['cat_rank'] = (int) preg_replace('/\D/', '', strip_tags($cells[1][1]));
            }
        }
    }

    return $result;
}



    public function getReport($id, $project)
    {
        $today = Carbon::now('America/Los_Angeles')->format('Y-m-d');

        $url = "https://chrome-stats.com/d/{$id}/trends";

        $ch = curl_init();
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7'));
        $result = curl_exec($ch);
        curl_close($ch);
        //$response = json_decode($result);
        $data = $this->parseChromeStats($result);

        $reports = Reports::firstOrNew(['date' => $today, 'project' => $project]);

        if (isset($data['users_total'])) {
            $reports->users_total = $data['users_total'];
        }
        if (isset($data['rating_value'])) {
            $reports->rating_value = $data['rating_value'];
        }
        if (isset($data['feedbacks_total'])) {
            $reports->feedbacks_total = $data['feedbacks_total'];
        }
        if (isset($data['overal_rank'])) {
            $reports->overal_rank = $data['overal_rank'];
        }
        if (isset($data['cat_rank'])) {
            $reports->cat_rank = $data['cat_rank'];
        }
       $reports->save();
        $this->info($reports);
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        date_default_timezone_set("America/Los_Angeles");
        $this->getReport('imomahaddnhnhfggpmpbphdiobpmahof', 'youtube_skins_com');
        $this->getReport('gideponcmplkbifbmopkmhncghnkpjng', 'ad_skipper');
        $this->getReport('oodajhdbojacdmkhkiafdhicifcdjoig', 'fb_zone');
        $this->getReport('oinkhgpjmeccknjbbccabjfonamfmcbn', 'cursor_land_com');
        $this->getReport('bmjmipppabdlpjccanalncobmbacckjn', 'cursor_style');
        return 0;
    }
}
