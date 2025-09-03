<?php

namespace App\Http\Controllers\Distributor;

use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function view()
    {
        $distributor = auth('distributor')->user();
        return view('distributor-views.profile.index', compact('distributor'));
    }

    public function update(Request $request)
    {
        $distributor = auth('distributor')->user();
        $request->validate([
            'f_name' => 'required|max:100',
            'l_name' => 'nullable|max:100',
            'email' => ['required', 'email', Rule::unique('vendors')->ignore($distributor->id)],
            'phone' => ['required', Rule::unique('vendors')->ignore($distributor->id)],
            'image' => 'nullable|max:2048',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000'
        ]);

        $distributor->f_name = $request->f_name;
        $distributor->l_name = $request->l_name;
        $distributor->phone = $request->phone;
        $distributor->email = $request->email;
        $distributor->address = $request->address;
        $distributor->city = $request->city;
        $distributor->state = $request->state;
        $distributor->zip_code = $request->zip_code;
        $distributor->description = $request->description;

        if ($request->hasFile('image')) {
            // Remove a imagem anterior se existir
            if ($distributor->image && Storage::disk('public')->exists('distributor/' . $distributor->image)) {
                Storage::disk('public')->delete('distributor/' . $distributor->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            Storage::putFileAs('public/distributor', $image, $imageName);
            $distributor->image = $imageName;
        }
        $distributor->save();

        Toastr::success(translate('messages.profile_updated_successfully'));
        return back();
    }

    public function settings_password_update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'same:confirm_password', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
            'confirm_password' => 'required',
        ],[
            'password.min_length' => translate('The password must be at least :min characters long'),
            'password.mixed' => translate('The password must contain both uppercase and lowercase letters'),
            'password.letters' => translate('The password must contain letters'),
            'password.numbers' => translate('The password must contain numbers'),
            'password.symbols' => translate('The password must contain symbols'),
            'password.uncompromised' => translate('The password is compromised. Please choose a different one'),
            'password.custom' => translate('The password cannot contain white spaces.'),
        ]);

        $distributor = auth('distributor')->user();
        $distributor->password = bcrypt($request['password']);
        $distributor->save();
        Toastr::success(translate('messages.distributor_password_updated_successfully'));
        return back();
    }

    /**
     * Atualiza configurações de pagamento
     */
    public function updatePaymentSettings(Request $request)
    {
        $request->validate([
            'pix_key' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'account_no' => 'nullable|string|max:50',
            'branch' => 'nullable|string|max:20',
            'holder_name' => 'nullable|string|max:100'
        ]);

        $distributor = auth('distributor')->user();
        
        $distributor->pix_key = $request->pix_key;
        $distributor->bank_name = $request->bank_name;
        $distributor->account_no = $request->account_no;
        $distributor->branch = $request->branch;
        $distributor->holder_name = $request->holder_name;
        $distributor->save();

        Toastr::success('Configurações de pagamento atualizadas com sucesso!');
        return back();
    }

    /**
     * Atualiza configurações de entrega
     */
    public function updateDeliverySettings(Request $request)
    {
        $request->validate([
            'delivery_time' => 'nullable|integer|min:1|max:168',
            'minimum_order' => 'nullable|numeric|min:0',
            'delivery_charge' => 'nullable|numeric|min:0',
            'free_delivery_over' => 'nullable|numeric|min:0',
            'delivery_radius' => 'nullable|numeric|min:0',
            'delivery_areas' => 'nullable|string|max:1000'
        ]);

        $distributor = auth('distributor')->user();
        
        $distributor->delivery_time = $request->delivery_time;
        $distributor->minimum_order = $request->minimum_order;
        $distributor->delivery_charge = $request->delivery_charge;
        $distributor->free_delivery_over = $request->free_delivery_over;
        $distributor->delivery_radius = $request->delivery_radius;
        $distributor->delivery_areas = $request->delivery_areas;
        $distributor->save();

        Toastr::success('Configurações de entrega atualizadas com sucesso!');
        return back();
    }
}
