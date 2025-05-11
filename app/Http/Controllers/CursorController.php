<?php

namespace App\Http\Controllers;

use \App;
use Illuminate\Http\Request;
use App\Models\categories;
use App\Models\cursor;
use App\Models\bg;
use App\Models\Animated;
use Validator;
use DB;
use File;
use Imagick;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class CursorController extends Controller {

    public function create(Request $request)
    {
        $categories = categories::orderBy('id', 'DESC')->get();
        $cursors = cursor::orderBy('id', 'DESC')->where('cat', '=', $request->cat)->get();
    
        return view('admin.cursors.create', compact('categories', 'cursors'));
    }


    public function destroy($id)
    {
        $cursor = cursor::findOrFail($id);
    
        // Видаляємо SVG файли (зберігались у storage)
        $svgCursor = storage_path('app/public/cursors_new/' . $cursor->c_file);
        $svgPointer = storage_path('app/public/pointers_new/' . $cursor->p_file);
        if (File::exists($svgCursor)) File::delete($svgCursor);
        if (File::exists($svgPointer)) File::delete($svgPointer);
    
        // Видалення з мовного файлу
        $langPath = resource_path('lang/en/cursors.php');
        if (File::exists($langPath)) {
            $translations = include $langPath;
            unset($translations['c_' . $cursor->id]);
            File::put($langPath, "<?php\n\nreturn " . var_export($translations, true) . ";\n");
        }
    
        $cursor->delete();
    
        return response()->json(['success' => true]);
    }

    
    public function reinitCursorLang()
    {
        $cursors = cursor::select('id', 'name_en')->get();
        $path = resource_path('lang/en/cursors.php');
    
        $translations = File::exists($path) ? include $path : [];
    
        foreach ($cursors as $c) {
            $key = 'c_' . $c->id;
    
            if (!isset($translations[$key])) {
                $translations[$key] = $c->name_en;
            }
        }
    
        // (Опційно) сортувати ключі
        ksort($translations);
    
        // Запис у файл
        $output = "<?php\n\nreturn [\n";
        foreach ($translations as $k => $v) {
            $escaped = addslashes($v);
            $output .= "    '$k' => '$escaped',\n";
        }
        $output .= "];\n";
    
        File::put($path, $output);
    
        return '✅ cursors.php updated successfully.';
    }

    public function reinitDb()
    {
        $categories = categories::select('alt_name', 'base_name_en', 'description', 'short_descr')->get();
        $path = resource_path('lang/en/collections.php');
    
        // Завантажити існуючий словник
        $translations = File::exists($path) ? include $path : [];
    
        foreach ($categories as $cat) {
            $key = $cat->alt_name;
    
            if (!isset($translations[$key])) {
                $translations[$key] = $cat->base_name_en;
            }
    
            if (!isset($translations["{$key}_descr"])) {
                $translations["{$key}_descr"] = $cat->description;
            }
    
            if (!isset($translations["{$key}_short_descr"])) {
                $translations["{$key}_short_descr"] = $cat->short_descr;
            }
        }
    
        // Побудувати новий PHP-файл
        $output = "<?php\n\nreturn [\n";
        foreach ($translations as $k => $v) {
            $escaped = addslashes($v);
            $output .= "    '$k' => '$escaped',\n";
        }
        $output .= "];\n";
    
        // Записати назад у файл
        File::put($path, $output);
    
        $this->reinitCursorLang();
        return '✅ New keys added to collections.php (existing kept unchanged).';
    }
    
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
    
            'cat_id' => 'required|array',
            'cat_id.*' => 'required|exists:categories,id',
    
            'c_file' => 'required|array',
            'c_file.*' => 'required|file|mimes:png,jpg,jpeg,svg',
    
            'p_file' => 'required|array',
            'p_file.*' => 'required|file|mimes:png,jpg,jpeg,svg',
    
            'offsetX' => 'required|array',
            'offsetX.*' => 'required|integer|min:0',
    
            'offsetY' => 'required|array',
            'offsetY.*' => 'required|integer|min:0',
    
            'offsetX_p' => 'required|array',
            'offsetX_p.*' => 'required|integer|min:0',
    
            'offsetY_p' => 'required|array',
            'offsetY_p.*' => 'required|integer|min:0',
    
            'schedule' => 'required|date',
        ]);
    
        $count = count($request->input('name'));
    
        for ($i = 0; $i < $count; $i++) {
            try {
                $cursorFile = $request->file('c_file')[$i];
                $pointerFile = $request->file('p_file')[$i];
    
                $cFilePath = $cursorFile->store('public/cursors_new');
                $pFilePath = $pointerFile->store('public/pointers_new');
    
                $cursor = new Cursor();
                $cursor->name = $request->input('name')[$i];
                $cursor->name_en = $cursor->name;
                $cursor->name_es = $cursor->name;
                $cursor->cat = $request->input('cat_id')[$i];
                $cursor->c_file = basename($cFilePath);
                $cursor->p_file = basename($pFilePath);
                $cursor->offsetX = $request->input('offsetX')[$i];
                $cursor->offsetY = $request->input('offsetY')[$i];
                $cursor->offsetX_p = $request->input('offsetX_p')[$i];
                $cursor->offsetY_p = $request->input('offsetY_p')[$i];
                $cursor->schedule = $request->input('schedule');
                $cursor->save();
    
                // 💬 Update translations
                $langPath = resource_path('lang/en/cursors.php');
                $translations = File::exists($langPath) ? include($langPath) : [];
                $translations['c_' . $cursor->id] = $cursor->name;
                File::put($langPath, "<?php\n\nreturn " . var_export($translations, true) . ";\n");
    
            } catch (\Throwable $e) {
               
            }
        }
    
        return redirect()->route('cursors.create')->with('success', 'Cursors created successfully.');
    }
    
        
    public function setBgUser() {
        $r = bg::firstOrCreate(['date' => date('Y-m-d')]);
        $r->count = $r->count + 1;
        $r->save();
        // return view('other.bg_success');
        return redirect('https://vk.com');
    }

    public function getBgUser() {
        $r = bg::where('date', '=', date('Y-m-d'))->get();
        echo $r->first()->count;
    }

    public function setTopCursor(Request $request) {
        if ($request->type == 'stat') {
            $db = cursor::find($request->id);
            $db->top = $db->top + 1;
            $db->save();
        }
    }

    public function updateStatic(Request $request) {

        $db = cursor::find($request->id);

        $db->name = $request->c_name;
        $db->offsetX = $request->oX;
        $db->offsetY = $request->oY;
        $db->offsetX_p = $request->oXp;
        $db->offsetY_p = $request->oYp;
        $db->save();
        return redirect()->back();
    }

    public function reInitStatic() {
        $db = DB::table('cursors')->select('*')->where('updated_at', '<', Carbon::today()->toDateString())->limit(1)->inRandomOrder()->get();
        return view('admin.reinit', compact('db'));
    }

    public function getCode() {
        return view('code');
    }

    public function updateAni(Request $request) {

        $db = Animated::find($request->id);

        $db->name = $request->c_name;
        $db->offsetX = $request->oX;
        $db->offsetY = $request->oY;
        $db->offsetX_p = $request->oXp;
        $db->offsetY_p = $request->oYp;
        $db->save();
        return redirect()->back();
    }

    public function reInitAni() {
        $db = DB::table('animateds')->select('*')->where('updated_at', '<', Carbon::today()->toDateString())->limit(1)->inRandomOrder()->get();
        return view('admin.reinitani', compact('db'));
    }

    public function uploadDb() {
        $cur = DB::table('cursors')->
                        select('cursors.id', 'name', 'c_file', 'p_file', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p', 'cat', 'categories.base_name', 'categories.alt_name')->
                        join('categories', 'cursors.cat', '=', 'categories.id')->where('cat', '=', '18')->get();
        $obj = new \stdClass();

        $files = glob(resource_path('lang/en/*.php'));
        $strings = [];

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $strings[$name] = require $file;
        }




        foreach ($cur as $item) {
            if (!property_exists($obj, $item->alt_name)) {
                $obj->{$item->alt_name} = (object) array('id' => $item->cat, 'name' => $strings['collections'][$item->alt_name], 'items' => array());
            }


            $removed = ((intval($item->cat) != 18) && (intval($item->cat) != 16) && (intval($item->cat) != 9) && (intval($item->cat) != 2) && (intval($item->cat) != 1)) ? 1 : 0;

            $a = [
                "id" => $item->id,
                "name" => $strings['cursors']['c_' . $item->id],
                "removed" => $removed,
                "favorite" => 0,
                'cursor' => [
                    'path' => '{ext}resources/static/arrows/' . $item->c_file,
                    'offsetX' => $item->offsetX,
                    'offsetY' => $item->offsetY,
                ],
                'pointer' => [
                    'path' => '{ext}resources/static/pointers/' . $item->p_file,
                    'offsetX' => $item->offsetX_p,
                    'offsetY' => $item->offsetY_p,
                ],
            ];

            File::copy(public_path('/resources/cursors/') . $item->c_file, public_path('/resources/d_cursors/') . $item->c_file);
            File::copy(public_path('/resources/pointers/') . $item->p_file, public_path('/resources/d_pointers/') . $item->p_file);
            array_push($obj->{$item->alt_name}->items, $a);
        }
        //dd($obj);
        return view('other.curdb', ['curdb' => json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
        //return ['db' => ];
    }

    public function uploadAniDb() {
        $cur = DB::table('animateds')->
                        select('id', 'name', 'c_file', 'c_file_prev', 'p_file', 'p_file_prev', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p')->get();
        $obj = [];
        $u = 0;
        foreach ($cur as $item) {
            $u = $u + 1;
//            if (!property_exists($obj, $item->alt_name)) {
//                $obj->{$item->alt_name} = (object) array('id' => $item->cat, 'name' => $item->base_name, 'items' => array());
//            }
            if ($u < 4) {
                $removed = 0;
            } else
                $removed = 1;
            $a = [
                "id" => $item->id,
                "removed" => $removed,
                "name" => $item->name,
                'cursor' => [
                    'path' => '{ext}resources/animated/arrows/' . $item->c_file,
                    'prev' => '{ext}resources/animated/arrows/thumbs/' . $item->c_file_prev,
                    'offsetX' => $item->offsetX,
                    'offsetY' => $item->offsetY,
                ],
                'pointer' => [
                    'path' => '{ext}resources/animated/pointers/' . $item->p_file,
                    'prev' => '{ext}resources/animated/pointers/thumbs/' . $item->p_file_prev,
                    'offsetX' => $item->offsetX_p,
                    'offsetY' => $item->offsetY_p,
                ],
            ];

            array_push($obj, $a);
        }
        //dd($obj);
        return view('other.curdb', ['curdb' => json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
        //return ['db' => ];
    }

    public function compress_png($path_to_png_file, $max_quality = 60) {
        if (!file_exists($path_to_png_file)) {
            throw new \Exception("File does not exist: $path_to_png_file");
        }

        // guarantee that quality won't be worse than that.
        $min_quality = 50;

        // '-' makes it use stdout, required to save to $compressed_png_content variable
        // '<' makes it read from the given file path
        // escapeshellarg() makes this safe to use with any path
        $compressed_png_content = shell_exec("pngquant --speed=1 --quality=$max_quality - < " . escapeshellarg($path_to_png_file));

        if (!$compressed_png_content) {
            throw new \Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
        }

        return $compressed_png_content;
    }

    public function resize_image($file, $save_to, $w, $h) {
        $image = new \Imagick($file);
        $image->adaptiveSharpenImage(5, 1);
        $image->resizeImage(128, 128, Imagick::FILTER_LANCZOS, 1);
        $output = $image->getimageblob();
        file_put_contents(public_path('resources/temp/') . 'resized.png', $output);

        $image_c = $this->compress_png(public_path('resources/temp/') . 'resized.png');
        file_put_contents($save_to, $image_c);
    }

    public function delete(Request $request) {
        try {
            $cur = cursor::where('id', $request->id)->delete();
            File::delete(public_path('resources/cursors/' . $request->c_file));
            File::delete(public_path('resources/pointers/' . $request->p_file));
        } catch (\Throwable $e) {
            return ['result' => false, 'message' => $e->getMessage()];
        }
        return ['result' => true];
    }

    public function deleteAnimated(Request $request) {
        try {
            $cur = Animated::where('id', $request->id)->delete();
            File::delete(public_path('resources/animated/cursors/' . $request->c_file));
            File::delete(public_path('resources/animated/pointers/' . $request->p_file));
        } catch (\Throwable $e) {
            return ['result' => false, 'message' => $e->getMessage()];
        }
        return ['result' => true];
    }

    public function getAll() {
        $cur = DB::table('cursors')->select('cursors.id', 'name', 'c_file', 'p_file', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p', 'cat', 'top', 'cursors.created_at', 'base_name', 'alt_name')->join('categories', 'cursors.cat', '=', 'categories.id')->get();
        return ['data' => $cur];
    }

    public function getAllAnimated() {
        $cur = DB::table('animateds')->select('id', 'name', 'c_file', 'p_file', 'c_file_prev', 'p_file_prev', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p', 'created_at')->get();
        return ['data' => $cur];
    }

    public function show() {
        return view('admin.cursors');
    }

    public function getCat() {
        $all_cat = categories::all();
        $r = array();
        foreach ($all_cat as $cat) {
            $r[] = ['id' => $cat->id, 'base_name' => $cat->base_name, 'alt_name' => $cat->alt_name];
        }
        echo json_encode($r);
    }

    public function trans() {
        $lang_path = App::langPath();
        $ru = include($lang_path . '/ru/collections.php');
        $en = include($lang_path . '/en/collections.php');
        $es = include($lang_path . '/es/collections.php');
        foreach ($en as $key => $value){           
            $cat = categories::where('alt_name', '=', $key)->first();
            if ($cat) {
                $cat->base_name_en = $value;
                $cat->save();
            }
        }
        
        foreach ($es as $key => $value){           
            $cat = categories::where('alt_name', '=', $key)->first();
            if ($cat) {
                $cat->base_name_es = $value;
                $cat->save();
            }
        }        
        
    }

    public function setCategoryLang($alt, $base_name, $base_name_en, $base_name_es, $descr, $descr_en, $descr_es, $short_descr, $short_descr_en, $short_descr_es) {
        $lang_path = App::langPath();
        $ru = include($lang_path . '/ru/collections.php');
        $en = include($lang_path . '/en/collections.php');
        $es = include($lang_path . '/es/collections.php');

//        PUT RU DATA
        $ru = [$alt => $base_name] + $ru;
        $ru = [$alt . '_short_descr' => $short_descr] + $ru;
        $ru = [$alt . '_descr' => $descr] + $ru;
        $ru_data = "<?php\n return [";
        foreach ($ru as $key => $value) {
            $ru_data = $ru_data . "\n\t'" . $key . "' => '" . addslashes($value) . "',";
        }
        $ru_data .= "\n];";
        file_put_contents($lang_path . '/ru/collections.php', $ru_data);

//        PUT EN DATA
        $en = [$alt => $base_name_en] + $en;
        $en = [$alt . '_short_descr' => $short_descr_en] + $en;
        $en = [$alt . '_descr' => $descr_en] + $en;
        $en_data = "<?php\n return [";
        foreach ($en as $key => $value) {
            $en_data = $en_data . "\n\t'" . $key . "' => '" . addslashes($value) . "',";
        }
        $en_data .= "\n];";
        file_put_contents($lang_path . '/en/collections.php', $en_data);

//        PUT ES DATA
        $es = [$alt => $base_name_es] + $es;
        $es = [$alt . '_short_descr' => $short_descr_es] + $es;
        $es = [$alt . '_descr' => $descr_es] + $es;
        $es_data = "<?php\n return [";
        foreach ($es as $key => $value) {
            $es_data = $es_data . "\n\t'" . $key . "' => '" . addslashes($value) . "',";
        }
        $es_data .= "\n];";
        file_put_contents($lang_path . '/es/collections.php', $es_data);
    }

    public function saveCat(request $request) {
        $validation = Validator::make($request->all(), [
                    'base_name' => 'required|min:3',
                    'base_name_en' => 'required|min:3',
                    'base_name_es' => 'required|min:3',
                    'alt_name' => 'required|min:3',
                    'descr' => 'required|min:10',
                    'descr_en' => 'required|min:10',
                    'descr_es' => 'required|min:10',
                    'short_descr' => 'required|min:10',
                    'short_descr_en' => 'required|min:10',
                    'short_descr_es' => 'required|min:10',
                    'inputGroupFile01' => 'required|max:2048',
        ]);

        if ($validation->passes()) {
            try {
                $image_cat = $request->file('inputGroupFile01');
                $name_c = uniqid() . '.' . $image_cat->getClientOriginalExtension();
                $image_cat->move(public_path('resources/categories'), $name_c);

                $cat = new categories;
                $cat->base_name = $request->base_name;
                $cat->base_name_en = $request->base_name_en;
                $cat->base_name_es = $request->base_name_es;
                $cat->alt_name = $request->alt_name;
                $cat->description = $request->descr;
                $cat->short_descr = $request->short_descr;
                $cat->img = $name_c;
                $cat->save();
                $this->setCategoryLang($request->alt_name, $request->base_name, $request->base_name_en, $request->base_name_es, $request->descr, $request->descr_en, $request->descr_es, $request->short_descr, $request->short_descr_en, $request->short_descr_es);
                return ['message' => 'Succesfully created', 'result' => true];
            } catch (\Exception $e) {
                return ['message' => 'Categoy already exist', 'result' => false];
            }
        } else
            return ['message' => $validation->errors()->all(), 'result' => false];

    }

    public function setCursorLang($id, $ru_val, $en_val, $es_val) {
        $lang_path = App::langPath();

        $ru = include($lang_path . '/ru/cursors.php');
        $en = include($lang_path . '/en/cursors.php');
        $es = include($lang_path . '/es/cursors.php');

//        PUT EN DATA
        $en = ['c_' . $id => $en_val] + $en;
        $en_data = "<?php\n return [";
        foreach ($en as $key => $value) {
            $en_data = $en_data . "\n\t'" . $key . "' => '" . addslashes($value) . "',";
        }
        $en_data .= "\n];";
        file_put_contents($lang_path . '/en/cursors.php', $en_data);

//        PUT RU DATA
        $ru = ['c_' . $id => $ru_val] + $ru;
        $ru_data = "<?php\n return [";
        foreach ($ru as $key => $value) {
            $ru_data = $ru_data . "\n\t'" . $key . "' => '" . addslashes($value) . "',";
        }
        $ru_data .= "\n];";
        file_put_contents($lang_path . '/ru/cursors.php', $ru_data);

//        PUT ES DATA
        $es = ['c_' . $id => $es_val] + $es;
        $es_data = "<?php\n return [";
        foreach ($es as $key => $value) {
            $es_data = $es_data . "\n\t'" . $key . "' => '" . addslashes($value) . "',";
        }
        $es_data .= "\n];";
        file_put_contents($lang_path . '/es/cursors.php', $es_data);

    }

    function upload(Request $request) {
        $ds = DB::table('cursors as c')->select(DB::raw('c.schedule'))
                ->from(DB::raw('(SELECT * FROM cursors ORDER BY schedule DESC LIMIT 1) c'))
                ->groupBy('c.schedule')
                ->orderBy('c.schedule', 'DESC')
                ->get();

        $last_count = $ds->count();
        if ($last_count == 1) {
            $last_published = strtotime($ds[0]->schedule);
            $schedule = date('y-m-d', strtotime("+2 day", $last_published));
        } else if ($last_count > 1) {
            $last_published = strtotime($ds[0]->schedule);
            $schedule = date("y-m-d", $last_published);
        } else {
            $schedule = date("y-m-d");
        }

        if ($request->exists('publish_now')) {
            $schedule = date("y-m-d");
        }


        $validation = Validator::make($request->all(), [
                    'cursorUpload' => 'required|max:2048',
                    'pointerUpload' => 'required|max:2048',
                    'cat_input' => 'required',
                    'c_name' => 'required',
                    'c_name_en' => 'required',
                    'c_name_es' => 'required'
        ]);
        if ($validation->passes()) {
            $cat = $request->cat_input;
            $c_name = $request->c_name;
            $c_name_en = $request->c_name_en;
            $c_name_es = $request->c_name_es;
            $image_c = $request->file('cursorUpload');
            $image_p = $request->file('pointerUpload');
            $name_c = uniqid() . '.' . $image_c->getClientOriginalExtension();
            $name_p = uniqid() . '.' . $image_p->getClientOriginalExtension();
            $msg = [];
            if (explode('.', $name_c)[1] == 'png') {
                $image_c->move(public_path('resources/temp'), '1.png');
                $image_c = public_path('resources/temp/') . '1.png';
                //$image_c = $this->compress_png($image_c);
                file_put_contents(public_path('resources/cursors/') . $name_c, $image_c);
                //$this->resize_image(public_path('resources/temp/') . '1.png', public_path('resources/cursors/thumb/') . $name_c, 128, 128);

                $msg[] = ['result' => 'png:  ' . 'resources/cursors/' . $name_c];
            }

            if (explode('.', $name_p)[1] == 'png') {
                $image_p->move(public_path('resources/temp'), '2.png');
                $image_p = public_path('resources/temp/') . '2.png';
                //$image_p = $this->compress_png($image_p);
                file_put_contents(public_path('resources/pointers/') . $name_p, $image_p);
                //$this->resize_image(public_path('resources/temp/') . '2.png', public_path('resources/pointers/thumb/') . $name_p, 128, 128);
                $msg[] = ['result' => 'png:  ' . 'resources/pointers/' . $name_p];
            }

            if (explode('.', $name_p)[1] != 'png') {
                $image_c->move(public_path('resources/cursors'), $name_c);
                $image_p->move(public_path('resources/pointers'), $name_p);
//                $msg[] = ['result' => 'not png:  ' . 'resources/cursors/' . $name_c];
//                $msg[] = ['result' => 'not png:  ' . 'resources/pointers/' . $name_p];
            }

            $cur = new \App\Models\cursor;
            $cur->name = $c_name;
            $cur->name_en = $request->c_name_en;
            $cur->name_es = $request->c_name_es;

            $cur->c_file = $name_c;
            $cur->p_file = $name_p;

            $cur->offsetX = $request->oX;
            $cur->offsetY = $request->oY;
            $cur->offsetX_p = $request->oXp;
            $cur->offsetY_p = $request->oYp;
            $cur->schedule = $schedule;
            $cur->cat = $cat;
            $cur->save();

            $lastid = json_encode(['lastId' => $cur->id]);
            $this->setCursorLang($cur->id, $c_name, $c_name_en, $c_name_es);
            try {
                file_put_contents('/home/admin/web/api.cursor.style/public_html/request.json', $lastid);
            } catch (\Throwable $e) {
                
            }

            return response()->json([
                        'result' => 'true',
                        'id' => $cur->id,
                        'name_c' => 'resources/cursors/' . $name_c,
                        'name_p' => 'resources/pointers/' . $name_p,
                        'cat' => $cat,
                        'c_name' => $c_name
            ]);
        } else {
            return response()->json([
                        'message' => $validation->errors(),
            ]);
        }
    }

    function uploadAnimated(Request $request) {
        $validation = Validator::make($request->all(), [
                    'cursorUpload' => 'required|max:2048',
                    'pointerUpload' => 'required|max:2048',
                    'cursorUpload_prev' => 'required|max:2048',
                    'pointerUpload_prev' => 'required|max:2048',
                    'c_name' => 'required',
        ]);
        if ($validation->passes()) {
            $c_name = $request->c_name;

            $image_c = $request->file('cursorUpload');
            $image_p = $request->file('pointerUpload');
            $image_c_prev = $request->file('cursorUpload_prev');
            $image_p_prev = $request->file('pointerUpload_prev');

            $name_c = uniqid() . '.' . $image_c->getClientOriginalExtension();
            $name_p = uniqid() . '.' . $image_p->getClientOriginalExtension();
            $name_c_prev = uniqid() . '.' . $image_c_prev->getClientOriginalExtension();
            $name_p_prev = uniqid() . '.' . $image_p_prev->getClientOriginalExtension();

            $msg = [];
            if (explode('.', $name_c)[1] == 'png') {
                $image_c->move(public_path('resources/animated/cursors/'), $name_c);
            }

            if (explode('.', $name_p)[1] == 'png') {
                $image_p->move(public_path('resources/animated/pointers/'), $name_p);
            }

            if (explode('.', $name_c_prev)[1] == 'png') {
                $image_c_prev->move(public_path('resources/temp/'), '3.png');
                $image_c_prev = public_path('resources/temp/') . '3.png';
                $image_c_prev = $this->compress_png($image_c_prev);
                file_put_contents(public_path('resources/animated/cursors/prev/') . $name_c_prev, $image_c_prev);
                $this->resize_image(public_path('resources/temp/') . '3.png', public_path('resources/animated/cursors/thumb/') . $name_c_prev, 128, 128);
            }

            if (explode('.', $name_p_prev)[1] == 'png') {
                $image_p_prev->move(public_path('resources/temp/'), '4.png');
                $image_p_prev = public_path('resources/temp/') . '4.png';
                $image_p_prev = $this->compress_png($image_p_prev);
                file_put_contents(public_path('resources/animated/pointers/prev/') . $name_p_prev, $image_p_prev);
                $this->resize_image(public_path('resources/temp/') . '4.png', public_path('resources/animated/pointers/thumb/') . $name_p_prev, 128, 128);
            }


            $cur = new \App\Models\Animated;
            $cur->name = $c_name;
            $cur->c_file = $name_c;
            $cur->p_file = $name_p;
            $cur->c_file_prev = $name_c_prev;
            $cur->p_file_prev = $name_p_prev;
            $cur->offsetX = $request->oX;
            $cur->offsetY = $request->oY;
            $cur->offsetX_p = $request->oXp;
            $cur->offsetY_p = $request->oYp;
            $cur->save();

            return response()->json([
                        'result' => 'true',
                        'id' => $cur->id,
                        'name_c' => 'resources/cursors/' . $name_c,
                        'name_p' => 'resources/pointers/' . $name_p,
                        'c_name' => $c_name
            ]);
        } else {
            return response()->json([
                        'message' => $validation->errors(),
            ]);
        }
    }

}
