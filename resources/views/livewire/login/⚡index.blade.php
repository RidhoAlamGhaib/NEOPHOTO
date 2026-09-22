<?php
use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\customer;
use App\Models\OrderItemAddon;
use SweetAlert2\Laravel\Traits\WithSweetAlert;
use SweetAlert2\Laravel\Swal;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Process\Process;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
new class extends Component
{
     use WithSweetAlert;
     
    public $name = "";
    public $password = "";
    

   public function mount()
{
    
}
public function authenticate(Request $request) 
    {
        $credentials = $this->validate([
            'name' => ['required'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if(str_contains(Auth::user()->outlet, 'admin')){
                return $this->redirect('/dashboard');
            }else{
            return $this->redirect('/');
        }}
 
        return back()->withErrors([
            'name' => 'Akun Tidak Ditemukan!',
        ]);
    }
};
?>
<section class="vh-100">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5">
        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
          class="img-fluid" alt="Sample image">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
        <form wire:submit="authenticate()">
          <!-- Email input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <label class="form-label fs-3 fw-bold" name="name" for="form3Example3">ID Kasir</label>
            <input type="text" id="form3Example3" class="form-control form-control-lg" wire:model="name"
              placeholder="Kasir ID" />
              @error('name') <span class="error">{{ $message }}</span> @enderror
          </div>

          <!-- Password input -->
          <div data-mdb-input-init class="form-outline mb-3">
            <label class="form-label fs-3 fw-bold" for="form3Example4">Password</label>
            <input type="password" id="form3Example4" class="form-control form-control-lg" wire:model="password"
              placeholder="Password" />
              @error('password') <span class="error">{{ $message }}</span> @enderror
          </div>
          <div class="text-center text-lg-start mt-4 pt-2">
            <button  type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
              style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>

          </div>

        </form>
      </div>
    </div>
  </div>
  <div
    class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
    <!-- Copyright -->
    <div class="text-white mb-3 mb-md-0">
      Copyright © 2020. All rights reserved.
    </div>
    <!-- Copyright -->

    <!-- Right -->
    <div>
      <a href="#!" class="text-white me-4">
        <i class="fab fa-facebook-f"></i>
      </a>
      <a href="#!" class="text-white me-4">
        <i class="fab fa-twitter"></i>
      </a>
      <a href="#!" class="text-white me-4">
        <i class="fab fa-google"></i>
      </a>
      <a href="#!" class="text-white">
        <i class="fab fa-linkedin-in"></i>
      </a>
    </div>
    <!-- Right -->
  </div>
</section>