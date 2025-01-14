<?php

namespace App\Services\User;

use App\Models\PasswordRecovery;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordRecoveryMail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserService
{

    public function all()
    {
        try {
            $users = User::get();

            return ['status' => true, 'data' => $users];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term;

            $users = User::query();

            if(isset($search_term)){
                $users->where('name', 'LIKE', "%{$search_term}%")
                    ->orWhere('email', 'LIKE', "%{$search_term}%")
                    ->orWhere('phone', 'LIKE', "%{$search_term}%")
                    ->orWhere('address', 'LIKE', "%{$search_term}%")
                    ->orWhere('city', 'LIKE', "%{$search_term}%")
                    ->orWhere('region', 'LIKE', "%{$search_term}%")
                    ->orWhere('contry', 'LIKE', "%{$search_term}%")
                    ->orWhere('zip_code', 'LIKE', "%{$search_term}%");
            }

            $users = $users->paginate($perPage);

            return $users;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }  

    public function getUser()
    {
        try {
            $user = Auth::user();
    
            if ($user) {
                // Cast para o tipo correto
                $user = $user instanceof \App\Models\User ? $user : \App\Models\User::find($user->id);
    
                return ['status' => true, 'data' => $user];
            }
    
            return ['status' => false, 'error' => 'Usuário não autenticado', 'statusCode' => 401];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function cards()
    {
        try {
            $users = User::selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
                ->first();

            $users->inactive = $users->total - $users->active;

            return [
                'status' => true,
                'data' => [
                    'total' => $users->total,
                    'active' => $users->active,
                    'inactive' => $users->inactive
                ]
            ];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $request['image'] = $request['image'] == 'null' ? null : $request['image'];
            $request['is_active'] = $request['is_active'] == 'null' ? null : $request['is_active'];

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'max:255'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:10240'],
                'password' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:255'],
                'region' => ['required', 'string', 'max:255'],
                'contry' => ['required', 'string', 'max:255'],
                'zip_code' => ['required', 'string', 'max:255'],
                'is_active' => ['nullable', 'boolean'],
                'profile' => ['required', 'string', 'in:Admin,Client,consultant'],                
            ];
    
            $requestData = $request->all();
            $requestData['password'] = Hash::make($request->password);
    
            $validator = Validator::make($requestData, $rules);
    
            if ($validator->fails()) {
                throw new Exception($validator->errors(), 400);
            }
    
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('user_images', 'public');
                $fullPath = asset('storage/' . $path);
                $requestData['image'] = $fullPath;
            }
    
            $user = User::create($requestData);
        
            return ['status' => true, 'data' => $user];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    

    public function update($request, $user_id)
    {
        try {
            $request['image'] = $request['image'] == 'null' ? null : $request['image'];
            $request['is_active'] = $request['is_active'] == 'null' ? null : $request['is_active'];

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'max:255'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:10240'],
                'password' => ['nullable', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:255'],
                'region' => ['required', 'string', 'max:255'],
                'contry' => ['required', 'string', 'max:255'],
                'zip_code' => ['required', 'string', 'max:255'],
                'is_active' => ['nullable', 'boolean'],
                'profile' => ['required', 'string', 'in:Admin,Client,consultant'],                
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $userToUpdate = User::find($user_id);

            if(!isset($userToUpdate)) throw new Exception('Usuário não encontrado');

            $requestData = $validator->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('user_images', 'public');
                $fullPath = asset('storage/' . $path);
                $requestData['image'] = $fullPath;
            }

            $userToUpdate->update($requestData);

            return ['status' => true, 'data' => $userToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function userBlock($user_id)
    {
        try {
            $user = User::find($user_id);

            if (!$user) throw new Exception('Usuário não encontrado');

            $user->is_active = !$user->is_active;
            $user->save();

            return ['status' => true, 'data' => $user];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function requestRecoverPassword($request)
    {
        try {
            $email = $request->email;
            $user = User::where('email', $email)->first();

            if (!isset($user)) throw new Exception('Usuário não encontrado.');

            $code = bin2hex(random_bytes(10));

            $recovery = PasswordRecovery::create([
                'code' => $code,
                'user_id' => $user->id
            ]);

            if (!$recovery) {
                throw new Exception('Erro ao tentar recuperar senha');
            }

            Mail::to($email)->send(new PasswordRecoveryMail($code));
            return ['status' => true, 'data' => $user];

        } catch (Exception $error) {
            Log::error('Erro na recuperação de senha: ' . $error->getMessage());
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }


    public function updatePassword($request){
        try{
            $code = $request->code;
            $password = $request->password;

            $recovery = PasswordRecovery::orderBy('id', 'desc')
                ->where('code', $code)
                ->first();

            if(!$recovery) throw new Exception('Código enviado não é válido.');

            $user = User::find($recovery->user_id);
            $user->password = Hash::make($password);
            $user->save();
            $recovery->delete();

            return ['status' => true, 'data' => $user];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}
