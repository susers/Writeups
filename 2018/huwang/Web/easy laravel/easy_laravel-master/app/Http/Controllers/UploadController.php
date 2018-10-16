<?php

namespace App\Http\Controllers;

use Flash;
use Storage;
use Illuminate\Http\Request;
use App\Http\Requests\UploadRequest;

class UploadController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
        $this->path = storage_path('app/public');
    }

    public function index()
    {
        return view('upload');
    }

    public function upload(UploadRequest $request)
    {
        $file = $request->file('file');
        if (($file && $file->isValid())) {
            $allowed_extensions = ["bmp", "jpg", "jpeg", "png", "gif"];
            $ext = $file->getClientOriginalExtension();
            if(in_array($ext, $allowed_extensions)){
                $file->move($this->path, $file->getClientOriginalName());
                Flash::success('上传成功');
                return redirect(route('upload'));
            }
        }
        Flash::error('上传失败');
        return redirect(route('upload'));
    }

    public function files()
    {
        $files = array_except(Storage::allFiles('public'), ['0']);
        return view('files')->with('files', $files);
    }
 
    public function check(Request $request)
    {
        $path = $request->input('path', $this->path);
        $filename = $request->input('filename', null);
        if($filename){
            if(!file_exists($path . $filename)){
                Flash::error('磁盘文件已删除，刷新文件列表');
            }else{
                Flash::success('文件有效');
            }
        }
        return redirect(route('files'));
    }
}