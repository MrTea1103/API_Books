<?php

namespace App\Http\Controllers;

use App\Models\Books;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

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
            $rules = [
                'name' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
                'author' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
                'publishing_year' => 'required|numeric',
                'cate_id' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
            ];

            // Tạo một instance của Validator
            $validator = Validator::make($request->all(), $rules);

            // Kiểm tra xem validation có pass hay không
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            } else {

                try {

                    $data = Books::create([
                        'name' =>  $request->input('name'),
                        'author' =>  $request->input('author'),
                        'publishing_year' => $request->input('publishing_year'),
                        'cate_id' =>   $request->input('cate_id')


                    ]);

                    return response()->json($data);
                } catch (\Throwable $th) {
                    return
                        response()->json(['error' => 'Lỗi: ' . $th, 500]);
                }
            }
        } else {
            return response()->json(['message' => "Bạn ko có quyền vào đây ! Vui lòng không truy cập lung tung, xin cảm ơn !"]);
        }
    }


    public function update(Request $request, $id)
    {

        if (Auth::user()->role == 'admin') {

            $rules = [
                'name' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợỞíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
                'author' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợỞíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
                'publishing_year' => 'required|numeric',
                'cate_id' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợỞíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
            ];

            // Tạo một instance của Validator
            $validator = Validator::make($request->all(), $rules);

            // Kiểm tra xem validation có pass hay không
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            } else {

                try {

                    $data = Books::find($id);
                    $data->name = $request->input('name');
                    $data->author = $request->input('author');
                    $data->publishing_year = $request->input('publishing_year');
                    $data->cate_id = $request->input('cate_id');


                    $data->save();
                    return response()->json($data, 200);
                } catch (\Throwable $th) {
                    return
                        response()->json(['message' => 'Lỗi: ' . $th, 400]);
                }
            }
        } else {
            return response()->json(['message' => "Bạn ko có quyền vào đây ! Vui lòng không truy cập lung tung, xin cảm ơn !"]);
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

            if (!$data) {
                return response()->json(['message' => "ID bạn muốn xóa không tồn tại"], 401);
            } else {
                $data->delete();
                return response()->json(
                    [
                        'message' => 'Xóa thành công'
                    ],
                    200
                );
            }
        } else {
            return response()->json(['message' => "Bạn ko có quyền vào đây ! Vui lòng không truy cập lung tung, xin cảm ơn !"], 500);
        }
    }
}
