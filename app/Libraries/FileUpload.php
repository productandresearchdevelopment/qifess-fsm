<?php
namespace App\Libraries;

use Image;
use App\SystemModels\Globals\Upload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUpload{
    static function upload($param, $category=null){
        $upload = new FileUpload();
        $request = request();
        if($file = $request->file($param)){
            return $upload->uploadFile($file, $category);
        }
        else if($content = $request->input($param)){
            return $upload->uploadContent($content, $category);
        }
        return null;
    }

    static function push($dataFile, $category=null, $watermark=null){
        $upload = new FileUpload();
        if($dataFile){
            return $upload->pushContent($dataFile, $category, $watermark);
        }
        return null;
    }

    public function uploadFile($files, $category=null){
        if($files){
            if(gettype($files) == 'array'){
                $result = [];
                foreach ($files as $file) {
                    if($file){
                        $result[] = $this->pushFile($file, $category);
                    }
                }
                return $result;
            }
            else return $this->pushFile($files, $category);
        }
        return null;
    }

    public function pushFile($file, $category){
        $id         = (string) Str::uuid();
        $extension  = $file->extension();
        $filename   = $id.'.'.$extension;
        $mime       = $file->getMimeType();
        $type       = str_replace("/$extension", "", $mime);
        $origin     = $file->getClientOriginalName();
        $size       = $file->getSize();

        $input = [
            'id' => $id,
            'filename' => $filename,
            'category' => $category,
            'mime' => $mime,
            'type' => $type,
            'extension' => $extension,
            'size' => $size,
            'origin' => $origin,
        ];
        Upload::create($input);
        Storage::disk('public_uploads')->put($filename, file_get_contents($file));
        return $id;
    }

    public function uploadContent($contents, $category=null){
        if($contents){
            if(gettype($contents) == 'array'){
                $result = [];
                foreach ($contents as $content) {
                    if($content){
                        if($res =  $this->pushContent($content, $category)){
                            $result[] = $res;
                        }
                    }
                }
                return $result;
            }
            else return $this->pushContent($contents, $category);
        }
        return null;
    }

    public function pushContent($content, $category, $watermark=null){
        $data = explode(':', $content);
        if(count($data) > 1 && $data[0] == 'data') {
            $id   = (string) Str::uuid();
            $data = explode(';', $data[1]);
            if(count($data) > 1) {
                $mime  = $data[0];
                $mimes = explode('/', $mime);
                $data  = explode(',', $data[1]);
                if(count($mimes) > 1 && count($data) > 1) {
                    $data       = $data[1];
                    $type       = $mimes[0];
                    $extension  = $mimes[1];
                    $filename   = $id . '.' . $extension;
                    $origin     = $filename;
                    $size       = 0;

                    Storage::disk('public_uploads')->put($filename, base64_decode($data));
                    $size = Storage::disk('public_uploads')->size($filename);

                    if($watermark && $type == 'image'){
                        $pathfile = Storage::disk('public_uploads')->path($filename);
                        $img = Image::make($pathfile);
                        $img->text($watermark, 10, $img->height()-10, function ($font) {
                            $font->file(public_path('fonts/gleego.ttf'));
                            $font->size(18);
                            $font->color('#ff0000');
                            $font->align('left');
                            $font->valign('bottom');
                            $font->angle(0);
                        });
                        $img->save($pathfile);
                    }

                    $input = [
                        'id' => $id,
                        'filename' => $filename,
                        'category' => $category,
                        'mime' => $mime,
                        'type' => $type,
                        'extension' => $extension,
                        'size' => $size,
                        'origin' => $origin,
                    ];
                    Upload::create($input);

                    return $id;
                }
            }
        }
        return null;
    }

}
