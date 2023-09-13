<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource as UserResource;
use App\Models\KhachHangModel;
use App\Models\NhanVienModel;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $taikhoans = User::join('quyen_tai_khoan','quyen_tai_khoan.MaQuyen','=','users.MaQuyen')
                            ->where('users.TrangThai','!=',0)
                            ->select('users.*','quyen_tai_khoan.TenQuyen')
                            ->get();
        $arr=[
            'status' => true,
            'message' => 'Danh sách tài khoản',
            'data' => UserResource::collection($taikhoans),
        ];
        return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
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
            'maqtk' => 'required', 'emailnv' => 'required'
        ]);
        if ($validator->fails()){
            $arr = [
                'status' => false,
                'message' => 'Lỗi kiểm tra dữ liệu',
                'data' => $validator->errors()
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);            
        }
        $checkmail = Validator::make($input,[
            'emailnv' => 'email',
        ]);
        if ($checkmail->fails()){
            $arr = [
                'status' => false,
                'message' => 'Email không đúng định dạng',
                'data' => $checkmail->errors()
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);            
        }
        

        $email = $input['emailnv'];
        $check = User::where('email',$email)->count();
        if ($check !=0){
            $arr = [
                'status' => false,
                'message' => 'Email đã tồn tại',
                'data' => [],
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }

        $qtk = $input['maqtk'];
        User::insertGetId([
            'MaQuyen' => $qtk,            
            'email' => $email,    
            'password' => Hash::make('Cafenguyenchat@12345'), 
            'remember_token' => Str::random(10),         
            'updated_at' => date('Y-m-d h-i-s'),
            'created_at' => date('Y-m-d h-i-s'),
            'TrangThai' => 1,
        ]);
        $arr = [
            'status' => true,
            'message' => 'Tài khoản đã tạo thành công',
            'data' => [],
        ];
        return response()->json($arr,201,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ten)
    {
        //
        $taikhoan = User::where('email','like',"%$ten%")->get();
        if (is_null($taikhoan)){
            $arr = [
                'status' => false,
                'message' => 'Không có tài khoản này',
                'data' => [],
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);  
        }
        $arr = [
            'status' => true,
            'message' => 'Các tài khoản cần tìm',
            'data' => UserResource::collection($taikhoan),
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
    public function update(Request $request, $id)
    {
        //
        $input = $request->all();
        $validator = Validator::make($input,[
            'MaQuyen' => 'required', 'email' => 'required'
        ]);

        if ($validator->fails()){
            $arr = [
                'status' => false,
                'message' => 'Lỗi kiểm tra dữ liệu',
                'data' => $validator->errors()
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
        

        $email = $input['email'];
        $check = User::where('email',$email)->count();
        if ($check !=0){
            $arr = [
                'status' => false,
                'message' => 'Email đã tồn tại',
                'data' => [],
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }

        $qtk = $input['MaQuyen'];
        $taikhoan = User::where('id',$id)->update([
            'MaQuyen' => $qtk,            
            'email' => $email,
        ]);
        $arr = [
            'status' => true,
            'message' => 'Tài khoản đã cập nhật thành công',
            'data' => new UserResource($taikhoan),
        ];
        return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('id',$id)->first();
        User::where('id',$id)->update(['TrangThai' => 0]);
        if ($user->MaQuyen == 'KH'){            
            KhachHangModel::where('MaTK',$id)->update(['TrangThai' => 0]);
        }
        else{
            NhanVienModel::where('MaTK',$id)->update([
                'MaTK' => 1,
            ]);
        }
        $arr=[
            'status' => true,
            'message' => 'Tài khoản đã được xóa',
            'data' => [],
        ];
        return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    private function getToken($email, $password)
    {
        $token = null;
        try {
            if (!$token = JWTAuth::attempt( ['email'=>$email, 'password'=>$password])) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'Password or email is invalid',
                    'token'=>$token
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'Cannot create token',
            ]);
        }
        return $token;
    }

    public function register(Request $request)
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
        if ($input['password'] != $input['cfpass']){
            $arr = [
                'status' => false,
                'message' => 'Mật khẩu xác nhận không đúng',
                'data' => $checkmail->errors()
            ];
            return response()->json($arr,200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);            
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'data'=>$validator->errors()],200,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }

        $payload = [
            'password'=>Hash::make($request->password),
            'email'=>$request->email,
            'auth_token'=> ''
        ];

        $user = new User($payload);
        if ($user)
        {

            $token = self::getToken($request->email, $request->password);
            if (!is_string($token))  return response()->json(['success'=>false,'data'=>'Token generation failed'], 201);

            $user = User::where('email', $request->email)->get()->first();

            $user->auth_token = $token; 
            User::insertGetId([
                'MaQuyen' => 'KH',            
                'email' => $user->email,    
                'password' => $user->password, 
                'remember_token' => Str::random(10), 
                'auth_token' => $user->auth_token,        
                'email_verified_at' => date('Y-m-d h-i-s'),
                'updated_at' => date('Y-m-d h-i-s'),
                'created_at' => date('Y-m-d h-i-s'),
                'TrangThai' => 1,
            ]);
            
            $count = KhachHangModel::select('MaKH')->count();
            $makh = 'KH'.($count);
            $matk = User::where('email',$user->email)->select('MaTK')->first();
            $ho = $input['ho'];
            $ten = $input['ten'];
            $ngay = $input['ngaysinh'];
            $gioitinh = $input['gioitinh'];
            $dc = $input['diachi'];
            $sdt = $input['sdt'];
            $email = $user->email;
            KhachHangModel::insert([
                'MaKH' => $makh,
                'MaTK' => $matk->MaTK,
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
            $response = ['success'=>true,'auth_token'=>$token,'message'=>'Đăng ký thành công'];        
        }
        else
            $response = ['success'=>false, 'data'=>'Register Failed'];

        return response()->json($response, 201,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
         
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->get()->first();
        if ($user->TrangThai == 0){
            $response = ['status' => false,'message' => 'Tài khoản không tồn tại'];
        }
        else{
            if ($user && Hash::check($request->password, $user->password))
            {
                $token = self::getToken($request->email, $request->password);
                $user->auth_token = $token;
                $user->save();
                $nv = NhanVienModel::where('MaTK',$user->id)->first();
                $response=[
                    'status' => true,
                    'message' => 'Đăng nhập thành công',
                    'data' => ['auth_token' => $user->auth_token,'MaQuyen' => $user->MaQuyen,'MaNV' => $nv->MaNV],
                ];                     
            }
            else 
                $response = ['status' => false,'message' => 'Tài khoản hoặc mật khẩu không hợp lệ'];
        }
        return response()->json($response, 201,['Content-type','application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
