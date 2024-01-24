<?php

namespace App\Http\Controllers;

use App\Models\Books;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Books::all();

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        

        if (Auth::user()->role == 'admin') {
            try {
                $this->validate($request, [
                    'name' => 'required',
                    'author' => 'required',
                    'publishing_year' => 'required',
                    'cate_id' => 'required',
                ]);
                $data = new Books();

                $data->name = $request->input('name');
                $data->author = $request->input('author');
                $data->publishing_year = $request->input('publishing_year');
                $data->cate_id = $request->input('cate_id');

                $data->save();
                return response()->json($data);
            } catch (\Throwable $th) {
                return
                    response()->json('Lỗi: ' . $th, 500);
            }
        } else {
            return response()->json("Bạn ko có quyền vào đây ! Vui lòng không truy cập lung tung, xin cảm ơn !");
        }
    }


    public function update(Request $request, $id)
    {
       
        if (Auth::user()->role == 'admin') {
            try {
                $this->validate($request, [
                    'name' => 'required',
                    'author' => 'required',
                    'publishing_year' => 'required',
                    'cate_id' => 'required',
                ]);

                $data = Books::find($id);
                $data->name = $request->input('name');
                $data->author = $request->input('author');
                $data->publishing_year = $request->input('publishing_year');
                $data->cate_id = $request->input('cate_id');


                $data->save();
                return response()->json($data);
            } catch (\Throwable $th) {
                return
                    response()->json('error: ' . $th);
            }
        } else {
            return response()->json("Bạn ko có quyền vào đây ! Vui lòng không truy cập lung tung, xin cảm ơn !");
        }
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $results = Books::where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('author', 'like', "%{$keyword}%")
            ->orWhere('cate_id', 'like', "%{$keyword}%")
            ->get();
        return response()->json($results);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {

        if (Auth::user()->role == 'admin') {
            $data = Books::find($id);
            $data->delete();
            return response()->json(
                [
                'message'=>'Xóa thành công'
                ],
                200
            );
        } else {
            return response()->json("Bạn ko có quyền vào đây ! Vui lòng không truy cập lung tung, xin cảm ơn !");
        }
    }
   
 
}
