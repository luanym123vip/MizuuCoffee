<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ChiTietHoaDon as ChiTietHoaDonResource;
use App\Models\ChiTietHoaDonModel;
use Illuminate\Support\Facades\Validator;

class ChiTietHoaDonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     //
    //     $chitiethoadons = ChiTietHoaDonModel::all();
    //     $arr=[
    //         'status' => true,
    //         'message' => 'Danh sách chi tiết hóa đơn',
    //         'data' => ChiTietHoaDonResource::collection($chitiethoadons),
    //     ];
    //     return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    // }

    public function detail($id) // Tìm 1 sản phẩm theo mã sản phẩm
    {
        
        $hd = ChiTietHoaDonModel::where('MaHD',$id)->get(); 
        $arr = [
            'status' => true,
            'message' => 'Phiếu nhập hàng cần tìm',
            'data' => ChiTietHoaDonResource::collection($hd),
        ];
        return response()->json($arr,201,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        $validator = Validator::make($input,[
            'MaHD' => 'required', 'MaSP' => 'required','SoLuong' => 'required',
             'DonGia' => 'required', 'ThanhTien' => 'required',
        ]);

        if ($validator->fails()){
            $arr = [
                'status' => false,
                'message' => 'Lỗi kiểm tra dữ liệu',
                'data' => $validator->errors()
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);            
        }
        
        $mahd = $input['MaHD'];
        $masp = $input['MaSP'];
        $soluong = $input['SoLuong'];
        $dongia = $input['DonGia'];
        $thanhtien = $soluong * $dongia;
        ChiTietHoaDonModel::insert([
            'MaHD' => $mahd,
            'MaSP' => $masp,
            'SoLuong' => $soluong,
            'DonGia' => $dongia,
            'ThanhTien' => $thanhtien,
        ]);

        $arr = [
            'status' => true,
            'message' => 'Chi tiết hóa đơn đã tạo thành công',
        ];
        return response()->json($arr,201,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     //
    //     $chitiethoadon = ChiTietHoaDonModel::find($id);
    //     if (is_null($chitiethoadon)){
    //         $arr = [
    //             'status' => false,
    //             'message' => 'Không có chi tiết hóa đơn này',
    //             'data' => [],
    //         ];
    //         return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);  
    //     }
    //     $arr = [
    //         'status' => true,
    //         'message' => 'Chi tiết hóa đơn',
    //         'data' => new ChiTietHoaDonResource($chitiethoadon),
    //     ];
    //     return response()->json($arr,201,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, ChiTietHoaDonModel $chitiethoadon)
    // {
    //     //
    //     $input = $request->all();
    //     $validator = Validator::make($input,[
    //         'MaSP' => 'required','SoLuong' => 'required',
    //          'DonGia' => 'required', 'ThanhTien' => 'required',
    //     ]);

    //     if ($validator->fails()){
    //         $arr = [
    //             'status' => false,
    //             'message' => 'Lỗi kiểm tra dữ liệu',
    //             'data' => $validator->errors()
    //         ];
    //         return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);            
    //     }

    //     $chitiethoadon->MaSP = $input['MaSP'];
    //     $chitiethoadon->SoLuong = $input['SoLuong'];
    //     $chitiethoadon->DonGia = $input['DonGia'];
    //     $chitiethoadon->ThanhTien = $input['ThanhTien'];

    //     $arr = [
    //         'status' => true,
    //         'message' => 'Chi tiết hóa đơn đã cập nhật thành công',
    //         'data' => new ChiTietHoaDonResource($chitiethoadon),
    //     ];
    //     return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($mahd,$masp) // Xóa chi tiết hóa đơn
    {
        ChiTietHoaDonModel::where('MaHD',$mahd)->where('MaSP',$masp)->delete();
        $arr=[
            'status' => true,
            'message' => 'Chi tiết hóa đơn đã được xóa',
        ];
        return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
