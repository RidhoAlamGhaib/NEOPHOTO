// let total = 0;
// total = parseInt(document.getElementById("productPrice").value);
//  const rupiah = new Intl.NumberFormat('id-ID', {
//   style: 'currency',
//   currency: 'IDR',
//   minimumFractionDigits: 0 // Menghilangkan desimal ,00
// }).format(total);
//   document.getElementById("total").innerText = rupiah;

// function increase(x,y){
//   let el = document.getElementById(x+"-count");
  
//   let count = parseInt(el.value);;
//   let price = parseInt(y);

//   if (isNaN(count) ||!count) {
//     count = 0;
//   }

//   count++;

//   el.value = count;
//     const rupiah = new Intl.NumberFormat('id-ID', {
//   style: 'currency',
//   currency: 'IDR',
//   minimumFractionDigits: 0 // Menghilangkan desimal ,00
// }).format(price*count);
// let sumPrice = price*count;
// total = total+price;
//   document.getElementById(x+"-price").innerText = rupiah;

//   totalIncrease(total);
// }

//   function totalIncrease(x){
//     let total = document.getElementById("total");
//      const rupiah = new Intl.NumberFormat('id-ID', {
//     style: 'currency',
//     currency: 'IDR',
//     minimumFractionDigits: 0 // Menghilangkan desimal ,00
//   }).format(parseInt(x));
//     total.innerText = rupiah;
    
//   }


//   function decrease(x,y){
//   let productPrice = parseInt(document.getElementById("productPrice").innerText);
//   let el = document.getElementById(x+"-count");
//   let count = parseInt(el.value);;
//   let price = parseInt(y);

//   if (isNaN(count)) {
//     count = 0;
//   }
//   if(count <=1){
    
//     document.getElementById(x).classList.toggle("active");
//     document.getElementById("jumlah-"+x).classList.toggle("d-none");
//     document.getElementById(x+"-price").classList.toggle("d-none");
//     count = 0;
//     el.value = count;
//    const rupiah = new Intl.NumberFormat('id-ID', {
//   style: 'currency',
//   currency: 'IDR',
//   minimumFractionDigits: 0 // Menghilangkan desimal ,00
// }).format(price*count);
//   document.getElementById(x+"-price").innerText = rupiah;
//   total = total-(price);
//   console.log(total);
//   totalDecrease(total);
//   }else{
//     count--;
//     el.value = count;
//      const rupiah = new Intl.NumberFormat('id-ID', {
//     style: 'currency',
//     currency: 'IDR',
//     minimumFractionDigits: 0 // Menghilangkan desimal ,00
//   }).format(price*count);
//     document.getElementById(x+"-price").innerText = rupiah;
//     total = total-price;
//     console.log(total);
//     totalDecrease(total);
//   }


//   }

//   function totalDecrease(x){
//     let total = document.getElementById("total");
//      const rupiah = new Intl.NumberFormat('id-ID', {
//     style: 'currency',
//     currency: 'IDR',
//     minimumFractionDigits: 0 // Menghilangkan desimal ,00
//   }).format(parseInt(x));
//     total.innerText = rupiah;
//   }

// function swal() {
//     Swal.fire({
//       title: "One More Step",
//       input: "text",
//       inputLabel: "Approval Code Pembayaran",
//       inputPlaceholder: "Enter your approval code"
//     }).then((result) => {
//       if (result.isConfirmed) {
//         Swal.fire({
//           title: "Pembayaran Berhasil",
//           text: "Terima kasih telah melakukan pembelian di Neo Photo",
//           text: "Terima kasih telah melakukan pembelian di Neo Photo",
//           icon: "success",
//           confirmButtonText: "OK"
//         });
//       }
//     });
//   }
//  function activate(x){
//   if(x.classList.contains("active")){}
//   else{
//     x.classList.toggle("active");
//     document.getElementById("jumlah-"+x.id).classList.toggle("d-none");
//     document.getElementById(x.id+"-price").classList.toggle("d-none");
//     if(document.getElementById(x.id+"-count").value >=1){
   
//     }else{
//      document.getElementById("increase."+x.id).click();
//     }
//   }
// }

