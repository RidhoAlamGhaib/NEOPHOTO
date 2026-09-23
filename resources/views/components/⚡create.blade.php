<?php
use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

use SweetAlert2\Laravel\Traits\WithSweetAlert;
use SweetAlert2\Laravel\Swal;

class CheckoutComponent extends Component
{
     use WithSweetAlert;

    public function save(): void
    {
        // Simulate saving data to the database or performing some action
        // ...

        // After the action is performed, show a SweetAlert notification
        $this->alert('success', 'Pembayaran Berhasil', [
            'text' => 'Terima kasih telah melakukan pembelian di Neo Photo',
            'confirmButtonText' => 'OK'
        ]);
    }
};
?>

<div>


    <form class="text-center text-white needs-validation " action="/sell/" method="POST" @csrf >
  <div class="container-fluid d-flex justify-content-around align-items-center " style="background:#ffd5e8;height: 50vW;">
    <div class="col-6  py-5 px-5 " style="border-radius: 9%;background-color: #fab7be;box-shadow: 17px 10px 15px -3px rgba(0,0,0,0.1);">
          <h3 class="text-white fw-bold fs-1">Contact Information <div>
</div></h3>
      <div class="mb-3 d-none text-white text-start fs-4">
          <label for="exampleInputEmail1" name="productId" class="form-label text-white fw-bold"></label>
          <input type="text" class="form-control" required id="exampleInputEmail1" aria-describedby="emailHelp">
      </div>
      <div class="mb-3  text-white text-start fs-4">
          <label for="exampleInputEmail1" name="name" class="form-label text-white fw-bold">Nama</label>
          <input type="text" class="form-control " required id="exampleInputEmail1" aria-describedby="emailHelp">
           <div class="invalid-feedback">
            Isi Nama Kamu!  
          </div>
      </div>
      <div class="mb-3  text-white text-start fs-4">
          <label for="exampleInputEmail1" name="phone" class="form-label text-white fw-bold">Telpon</label>
          <input type="number" class="form-control" required id="exampleInputEmail1" aria-describedby="emailHelp">
          <input class="form-check-input "disabled checked type="checkbox" value="" id="flexCheckDefault">
          <label class="form-check-label text-white text-start fs-5" for="flexCheckChecked">
            Beritahu saya tentang promo dan update terbaru 
          </label>
      </div>
      <div class="mb-3  text-white text-start fs-4">
          <label for="exampleInputEmail1" name="email" class="form-label text-white fw-bold">Email</label>
          <input type="email" class="form-control" required id="exampleInputEmail1" aria-describedby="emailHelp">
      </div>
      <div class="mb-3  text-white text-start fs-4 mb-3">
          <label for="exampleInputEmail1" name="ig" class="form-label text-white fw-bold">IG</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
      </div>
  </div>
  <div class="col-4 h-75  px-2 " style="overflow-y:scroll;border-radius: 9%;background-color: #fab7be;box-shadow: 17px 10px 15px -3px rgba(0,0,0,0.1);">
      <h3 class="text-white fw-bold fs-1">Detail Pesanan</h3>
        <div class="container ">
          <div class="d-flex justify-content-between mb-3 border-bottom border-2 border-white align-items-center">
            <p class="text-white fw-bold fs-4"></p>
            <p class="text-white fw-bold fs-4"></p>
          </div>
          <div class="">
            <div class="d-flex  align-items-end  justify-content-between">
              <button  type="button" class="btn my-1 btn-outline-success"></button>
              <p   class="text-white d-none fs-5 fw-bold "></p>
            </div>
            <div class="d-flex  align-items-end d-none justify-content-end" >
            <button  id="" type="button" class="btn my-2 mx-1 btn-outline-success ">-</button>
            <input  min="0" class="btn my-2 fw-bold mx-1 btn-outline-success" value="0">
          <button type="button" class="btn my-2 mx-1 btn-outline-success">+</button>
            </div>
          </div>   
          @endforeach  
        </div>
          <div class="d-flex justify-content-between  border-top border-2 border-white align-items-center">
            <p class="text-white fw-bold fs-4">Total</p>
            <p id="total" class="text-white fw-bold fs-4"></p>
          </div>
      <div class="d-flex justify-content-between my-1 text-white">
        <a  data-bs-toggle="modal" data-bs-target="#staticBackdrop" class="btn btn-primary fs-5 rounded-pill px-3 fw-bold border-4 " style="background-color: #ffb3ba;border-color: #d6eadf;">pay <i class="text-white fa-solid fa-arrow-right"></i></a>
        <button type="submit" class="btn btn-primary">Understood</button>
      </div>
      <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Konfirmasi Pembayaran</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Understood</button>
      </div>
    </div>
  </div>
</div>
    </div>
  </div>
</form>
</div>