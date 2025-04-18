<?php

namespace App\Http\Controllers;

use \Cache;
use Illuminate\Http\Request;
use DB;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\uninstalled;
use Carbon\Carbon;
use App\Models\Cursor;
use App\Models\CursorTranslation;
use App\Models\categories;
use App\Models\Analytic;
use App;

class IndexController extends Controller {
    
    public $alt_name, $cat_id;

	public function debugPath(){
	return view('debug');
	}

    public function getCursor(Request $r) {
        dd($r);
    }

    public function cleanStr($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $string));
        return $string;
    }

    public function showCursorPreview(Request $r) {
        if (!$r->id) {
            abort(404);
        }

        $cursors = cursor::whereDate('schedule', '<=', Carbon::today())->where('id', '>=', $r->id)->orderBy('id', 'ASC')->limit(2);
        $cursors2 = cursor::whereDate('schedule', '<=', Carbon::today())->where('id', '<', $r->id)->orderBy('id', 'DESC')->limit(1)->union($cursors)->get();

        forEach ($cursors2 as $key => $cursor) {
            $cursors2[$key]->name_s = $this->cleanStr($cursors2[$key]->name_en);
            if ($key == 0) {
                $id_prev[] = $cursor->id;
                $id_prev[] = $cursors2[$key]->name_s;
            }
            if ($key == 2) {
                $id_next[] = $cursor->id;
                $id_next[] = $cursors2[$key]->name_s;
            }
        }

        if (!isset($id_prev))
            $id_prev = null;
        if (!isset($id_next))
            $id_next = null;

            $collections = categories::all();
    
            //$random_cat = $result = categories::inRandomOrder()->limit(3)->get();
            $random_cat = $collections->random(3);

            
        return view('cursor', ['random_cat' => $random_cat, 'all_cursors' => $cursors2, 'cursor' => $cursors2[1] ?? null, 'id_prev' => $id_prev, 'id_next' => $id_next]);
    }

    public function show(Request $r) {
        $order = 'asc';
        $sort = 'id';
    
        if ($r->type === 'popular') {
            $sort = 'top';
            $order = 'desc';
        } elseif ($r->type === 'new') {
            $sort = 'id';
            $order = 'desc';
        }
    
        $query = $r->q;
    
        // ⛏ Якщо є пошук — дістаємо id курсорів з cursor_translations
        if ($query) {
            $ids = \App\Models\CursorTranslation::where('name', 'LIKE', "%{$query}%")
                ->pluck('cursor_id')
                ->unique();
    
            $cursors = \App\Models\Cursor::whereIn('id', $ids)
                ->whereDate('schedule', '<=', \Carbon\Carbon::today())
                ->orderBy($sort, $order)
                ->paginate(32);
        } else {
            $cursors = \App\Models\Cursor::whereDate('schedule', '<=', \Carbon\Carbon::today())
                ->orderBy($sort, $order)
                ->paginate(32);
        }
    
        $collections = \App\Models\Categories::all();
    
        foreach ($cursors as $cursor) {
            $cursor->name_s = $this->cleanStr($cursor->name_en);
            $cursor->collection = $collections->first(fn($item) => $item->id == $cursor->cat);
        }
    
        return view('index', [
            'cursors' => $cursors,
            'query' => $query,
            'sort' => $sort
        ]);
    }
    

    public function showJsLang() {
        $lang = config('app.locale');

        $files = glob(resource_path('lang/' . $lang . '/*.php'));
        $strings = [];

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $strings[$name] = require $file;
        }
        $strings = json_encode($strings);

        header('Content-Type: text/javascript');
        echo('window.i18n = ' . $strings . ';');
        exit();
    }

    public function sendEmailFeedback(Request $request) {
        ini_set('memory_limit', '256M');
        $validation = Validator::make($request->all(), [
                    'message' => 'required|min:10|max:500',
                    'g-recaptcha-response' => 'recaptcha',
        ]);

        if ($validation->passes()) {
            $data['message'] = $request->header('User-Agent') . '<br>' . $request->message;
            $msg = '<b>' . $request->header('User-Agent') . '</b><hr><br>' . $request->message;
            try {
                Mail::send([], [], function ($message) use ($msg) {
                    $message->to('cursorstylemail@gmail.com', 'Cursor.Style')
                            ->subject('Uninstall')
                            ->setBody($msg, 'text/html');
                });
            } catch (\Throwable $e) {
                return ['result' => $e->getMessage()];
            }

            if (count(Mail::failures()) > 0) {
                return ['result' => Mail::failures()];
            } else
                return ['result' => true];
        } else {
            return $validation->errors();
        }
    }

    public function getAll() {
        $value = DB::table('cursors')->select('cursors.id', 'name', 'c_file', 'p_file', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p', 'cat', 'top')->join('categories', 'cursors.cat', '=', 'categories.id')->whereDate('schedule', '<=', Carbon::today())->get();
        return ['data' => $value];
    }

    public function getAllOrderById() {
        $value = DB::table('cursors')->select('cursors.id', 'name', 'c_file', 'p_file', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p', 'cat', 'top')->join('categories', 'cursors.cat', '=', 'categories.id')->whereDate('schedule', '<=', Carbon::today())->orderby('cursors.id')->get();
        return ['data' => $value];
    }

    public function getAllAnimatedOrderById() {
        $value = DB::table('animateds')->select('id', 'name', 'c_file', 'p_file', 'c_file_prev', 'p_file_prev', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p')->orderby('id')->get();
        return $value;
    }

    public function getAllByCat($cat) {
        $value = DB::table('cursors')->select('cursors.id', 'name', 'c_file', 'p_file', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p', 'cat', 'top', 'cursors.created_at', 'base_name', 'alt_name')
                        ->join('categories', 'cursors.cat', '=', 'categories.id')
                        ->whereDate('schedule', '<=', Carbon::today())
                        ->where('alt_name', $cat)->orderby('id', 'desc')->get();
        return $value;
    }

    public function getAllCategories() {
        $value = Cache::remember('users', 60, function () {
            return DB::table('categories')->select('id', 'base_name', 'alt_name', 'priority', 'description', 'short_descr', 'img')->inRandomOrder()->get();
        });
        
        //$value = DB::table('categories')->select('id', 'base_name', 'alt_name', 'priority', 'description', 'short_descr', 'img')->inRandomOrder()->get();
        return ['data_cat' => $value];
    }

    public function getCategories($cat) {
        $value = DB::table('categories')->select('*')->where('alt_name', $cat)->get();

        return $value;
    }

    public function getCategoriesRandom($cat) {
        return DB::table('categories')->select('*')->where('alt_name', '!=', $cat)->where('id', '<>', '29000')->inRandomOrder()->limit(3)->get();
    }

    public function getAllAnimated() {

        $value = DB::table('animateds')->select('id', 'name', 'c_file', 'p_file', 'c_file_prev', 'p_file_prev', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p')->get();

        $value = collect(json_decode($value));

        return $value;
    }

    public function showCat($alt_name, $success = false) {
        if (request()->getHost() == 'cursor.style') {
            $is_ru = true;
        } else {
            $is_ru = false;
        }
        
        $this->alt_name = $alt_name;        

        
        $collection = categories::where('alt_name', '=', $alt_name)->first();
        
        if (!$collection)
            abort(404);
        
        $items = cursor::whereDate('schedule', '<=', Carbon::today())->where('cat', '=', $collection->id)->orderBy('id')->get();

        for ($i = 0; $i < count($items); $i++) {
            $items[$i]->seo_link = $items[$i]->id . '/' . strtolower($alt_name) . '/' . strtolower(transliterator_transliterate('Russian-Latin/BGN', str_replace(' ', '_', $items[$i]->name)));
        }
        
        $collections = categories::all();
        
        forEach ($items as $key => $cursor) {            
            $this->cat_id = $items[$key]->cat;
            $items[$key]->name_s = $this->cleanStr($items[$key]->name_en);
            $items[$key]->collection = $collections->first(function($item) {return $item->id == $this->cat_id;});    
        }

        //$random_cat = $result = categories::inRandomOrder()->limit(3)->get();
        $random_cat = $collections->random(3);

        return view('cat', ['alt_name' => $alt_name, 'cursors' => $items, 'collection' => $collection, 'is_ru' => $is_ru, 'random_cat' => $random_cat]);
    }

    public function showAllCat2(Request $r) {
        $url = $_SERVER['REQUEST_URI'];
        $success = (strpos($url, 'success') > 0) ? true : false;
        $cats = $this->getAllCategories()['data_cat'];
        return view('allcat', compact('cats', 'success'));
    }

    public function showAllCat(Request $r) {
        if (request()->getHost() == 'cursor.style') {
            $is_ru = true;
        } else
            $is_ru = false;
        $url = $_SERVER['REQUEST_URI'];
        $success = (strpos($url, 'success') > 0) ? true : false;
        $cats = $this->getAllCategories()['data_cat'];
        return view('allcat', compact('cats', 'success', 'is_ru'));
    }

    public function sendEmail(Request $request) {
//        $validation = Validator::make($request->all(), [
//                    'name' => 'required|min:2|max:100',
//                    'email' => 'required|email',
//                    'subject' => 'required|min:2|max:100',
//                    'message' => 'required|min:10|max:500',
//                    'g-recaptcha-response' => 'recaptcha',
//        ]);
//        if ($validation->passes()) {
//
//            $data['name'] = $request->name;
//            $data['subject'] = $request->subject;
//            $data['email'] = $request->email;
//            $data['message'] = $request->message;
//
//            try {
//                Mail::send('mail', ['data' => $data], function ($message) use ($data) {
//                    $message->to('cursorstylemail@gmail.com', 'Cursor.Style')->subject($data['subject']);
//                    $message->from($data['email'], $data['name']);
//                });
//            } catch (\Throwable $e) {
//                return ['result' => $e->getMessage()];
//            }
//
//            if (count(Mail::failures()) > 0) {
//                return ['result' => Mail::failures()];
//            } else
//                return ['result' => true];
//        } else {
//            return $validation->errors();
//        }
        return view('contact', ['success' => 'true']);
    }

    public function setTopCursor(Request $request) {
//        if ($request->type == 'stat') {
//            $db = cursor::find($request->id);
//            $db->top = $db->top + 1;
//            $db->save();
//        }
    }

    public function successInstall(Request $request) {
//        if ($request->session()->has('installed')) {
//        } else {
//            $request->session()->put('installed', date("Ymd"));
//            $stat = Analytic::firstOrNew(['date' => date_format(\Carbon\Carbon::now(), 'Y-m-d')]);
//            $stat->date = \Carbon\Carbon::now();
//            $stat->increment('installs');
//            $stat->save();
//        }
        return view('other.success');
    }

    public function update() {
//        $stat = Analytic::firstOrNew(['date' => date_format(\Carbon\Carbon::now(), 'Y-m-d')]);
//        $stat->date = \Carbon\Carbon::now();
//        $stat->increment('response');
//        $stat->save();
        return ['result' => true];
    }

    public function showFeedback(Request $request) {
//        if (!$request->session()->has('installed')) {
//            $request->session()->put('installed', 'value');
//            $stat = Analytic::firstOrNew(['date' => date_format(\Carbon\Carbon::now(), 'Y-m-d')]);
//            $stat->date = \Carbon\Carbon::now();
//            $stat->increment('uninstalls');
//            $stat->save();
//        }
        return view('feedback');
    }

    public function mycollection(Request $request) {
        return view('mycollection');
    }    
}
