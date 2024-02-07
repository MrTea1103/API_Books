<?php

namespace App\Http\Controllers;

use App\Models\Books;
use Firebase\JWT\JWT;
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
    public function index(Request $request)
    {

        $sortField = $request->input('sort_field', 'title');
        $sortDirection = $request->input('sort_direction', 'asc');

        $query = Books::orderBy($sortField, $sortDirection);

        $books = $query->paginate(5);

        return response()->json($books);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //Kiểm tra vai trò
        if ($request->user()->role != 'admin') {
            return response()->json(['message' => "Bạn không có quyền vào đây! Vui lòng không truy cập lung tung, xin cảm ơn !"]);
        }

        //Kiển tra dữ liệu đầu vào
        $this->validate($request, [
            'name' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
            'author' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
            'publishing_year' => 'required|numeric',
            'cate_id' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
        ]);

        //Tạo mới dữ liệu
        try {
            $data = Books::create($request->all());
            return response()->json([
                'input' => $data,
                'message' => 'Thêm thành công!'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Lỗi: ' . $th, 500]);
        }
    }


    public function update(Request $request, $id)
    {
        //kiểm tra vai trò
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => "Bạn không có quyền vào đây! Vui lòng không truy cập lung tung, xin cảm ơn!"]);
        }

        //kiểm tra dữ liệu đầu vào
        $this->validate($request, [
            'name' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
            'author' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
            'publishing_year' => 'required|numeric',
            'cate_id' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
        ]);

        //Cập nhật dữ liệu
        try {
            $data = Books::find($id);
            $data->update($request->only(['name', 'author', 'publishing_year', 'cate_id']));

            return response()->json([
                'input' => $data, 
                'message: ' => 'cập nhật thành công !'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Lỗi: ' . $th, 400]);
        }
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

         //$results để tìm kết quả từ db  
        $results = Books::where(function ($query) use ($keyword) {
            $query->where('name', 'LIKE', "%{$keyword}%")
                ->orWhere('author', 'LIKE', "%{$keyword}%")
                ->orWhere('cate_id', 'LIKE', "%{$keyword}%");
        })->get();

        //Kiểm tra $results có tồn tại dữ liệu hay không bằng isEmpty()
        if ($results->isEmpty()) {
            return response()->json([
                'input: ' => 'Keyword: ' . $keyword, 
                'output: ' => 'từ khóa bạn tìm không tồn tại'
            ], 404);
        }
        return response()->json([
            'input: ' => 'Keyword: ' . $keyword, 
            'output: ' => $results
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $user = Auth::user();
        if ($user->role != 'admin') {
            return response()->json([
                'message' => "Bạn không có quyền vào đây! Vui lòng không truy cập lung tung, xin cảm ơn !"
            ],200);
        }

        $data = Books::find($id);

        if (!$data) {
            return response()->json([
                'input' => 'ID bạn muốn xóa: ' . $id,
                'message' => 'ID bạn muốn xóa không tồn tại'
            ],404);
        }
        
        $data->delete();
        return response()->json([
            'input' => 'ID bạn muốn xóa: '.$id,
            'output' => 'Xóa thành công!',
        ], 200);
    }
  
}
