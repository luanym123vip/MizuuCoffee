<?php

namespace App\Http\Controllers;

use App\Http\Resources\KhachHang as KhachHangResource;
use App\Models\KhachHangModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class KhachHangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() // Hàm lấy danh sách khách hàng
    {        
        $khachhangs = KhachHangModel::where('TrangThai','!=',0)->get();
        $arr=[
            'status' => true,
            'message' => 'Danh sách khách hàng',
            'data' => KhachHangResource::collection($khachhangs),
        ];
        return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function CheckInfor(Request $request) // Hàm thêm mới thông tin nhân viên
    {        
        $input = $request->all();
        $validator = Validator::make($input,[
            'ho' => 'required', 'ten' => 'required',
            'ngaysinh' => 'required', 'gioitinh' => 'required', 'diachi' => 'required','sdt' => 'required', 
            'email' => 'required'
        ]);
        
        if ($validator->fails()){
            $arr = [
                'status' => false,
                'message' => 'Chưa nhập đủ dữ liệu',
                'data' => $validator->errors(),
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }

        $checkphone = Validator::make($input,[
            'sdt' => 'regex:/^(0)+([0-9]{9})$/',
        ]);
        if ($checkphone->fails()){
            $arr = [
                'status' => false,
                'message' => 'Số điện thoại không đúng định dạng hoặc không đủ 10 chữ số',
                'data' => $checkphone->errors()
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);            
        }

        $checkmail = Validator::make($input,[
            'email' => 'email',
        ]);
        if ($checkmail->fails()){
            $arr = [
                'status' => false,
                'message' => 'Email không đúng định dạng',
                'data' => $checkmail->errors()
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);            
        }
        
        $arr = [
            'status' => true,
            'message' => 'Nhân viên đã thêm thành công',
        ];
        return response()->json($arr,201,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) // Hàm thêm mới thông tin khách hàng
    {        
        $input = $request->all();
        $validator = Validator::make($input,[
            'MaKH' => 'required', 'MaTK' => 'required', 'HoKH' => 'required', 'TenKH' => 'required',
            'NgaySinh' => 'required', 'GioiTinh' => 'required', 'DiaChi' => 'required','SoDienThoai' => 'required', 
            'Email' => 'required',
        ]);
        // Kiểm tra dữ liệu
        if ($validator->fails()){
            $arr = [
                'status' => false,
                'message' => 'Lỗi kiểm tra dữ liệu',
                'data' => $validator->errors(),
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
        
        $count = KhachHangModel::select('MaKH')->count();
        $makh = 'KH'+($count);
        $matk = $input['MaTK'];
        $ho = $input['HoKH'];
        $ten = $input['TenKH'];
        $ngay = $input['NgaySinh'];
        $gioitinh = $input['GioiTinh'];
        $dc = $input['DiaChi'];
        $sdt = $input['SoDienThoai'];
        $email = $input['Email'];
        KhachHangModel::insert([
            'MaKH' => $makh,
            'MaTK' => $matk,
            'HoKH' => $ho,
            'TenKH' => $ten,
            'NgaySinh' => $ngay,
            'GioiTinh' => $gioitinh,
            'DiaChi' => $dc,
            'SoDienThoai' => $sdt,
            'Email' => $email,
            'TrangThai' => 1,
            'updated_at' => date('Y-m-d h-i-s'),
        ]);

        $arr = [
            'status' => true,
            'message' => 'Khách hàng đã thêm thành công',
        ];
        return response()->json($arr,201,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ten) // Hàm lấy thông tin 1 khách hàng
    {        
        $khachhang = KhachHangModel::where('HoKH','like',"%$ten%")->orWhere('TenKH','like',"%$ten%")->get();
        if (is_null($khachhang)){
            $arr = [
                'status' => false,
                'message' => 'Không có khách hàng này',
                'data' => [],
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
        $arr = [
            'status' => true,
            'message' => 'Các khách hàng cần tìm',
            'data' => KhachHangResource::collection($khachhang),
        ];
        return response()->json($arr,201,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id) // Tìm 1 sản phẩm theo mã sản phẩm
    {
        $khachhang = KhachHangModel::where('MaKH',$id)->first();
        if (is_null($khachhang)){
            $arr = [
                'status' => false,
                'message' => 'Không có khách hàng này',
                'data' => [],
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);  
        }
        $arr = [
            'status' => true,
            'message' => 'Khách hàng cần tìm',
            'data' => new KhachHangResource($khachhang),
        ];
        return response()->json($arr,201,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) // Hàm cập nhật thông tin khách hàng
    {        
        $input = $request->all();
        $validator = Validator::make($input,[
            'HoKH' => 'required', 'TenKH' => 'required',
            'NgaySinh' => 'required', 'GioiTinh' => 'required', 'DiaChi' => 'required','SoDienThoai' => 'required', 
            'Email' => 'required',
        ]);
        // Kiểm tra dữ liệu
        if ($validator->fails()){
            $arr = [
                'status' => false,
                'message' => 'Lỗi kiểm tra dữ liệu',
                'data' => $validator->errors()
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);            
        }

        $ho = $input['HoKH'];
        $ten = $input['TenKH'];
        $ngay = $input['NgaySinh'];
        $gioitinh = $input['GioiTinh'];
        $dc = $input['DiaChi'];
        $sdt = $input['SoDienThoai'];
        $email = $input['Email'];
        KhachHangModel::where('MaKH',$id)->update([
            'HoKH' => $ho,
            'TenKH' => $ten,
            'NgaySinh' => $ngay,
            'GioiTinh' => $gioitinh,
            'DiaChi' => $dc,
            'SoDienThoai' => $sdt,
            'Email' => $email,
        ]);

        $arr = [
            'status' => true,
            'message' => 'Khách hàng đã cập nhật thành công',
        ];
        return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) // Xóa khách hàng (xóa ẩn)
    {        
        KhachHangModel::where('MaKH',$id)->update(['TrangThai' => 0]);
        $arr=[
            'status' => true,
            'message' => 'Khách hàng đã được xóa',
        ];
        return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
