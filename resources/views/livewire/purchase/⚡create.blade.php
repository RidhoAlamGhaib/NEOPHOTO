<?php
use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\customer;
use App\Models\OrderItemAddon;
use SweetAlert2\Laravel\Traits\WithSweetAlert;
use SweetAlert2\Laravel\Swal;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Carbon\Carbon;

new class extends Component
{
    use WithSweetAlert;

    public $products;
    public $order;
    public $orderItem;
    public $orderAddon;
    public $addon;
    public $id;

    #[Validate('required|min:5')]
    public $name = '';

    #[Validate('required|min:5')]
    public $telp = '';
    public $email = '';
    public $instagram = '';
    public $appr = '';
    public $paid = '';
    public $coupon = '';
    public $couponchck = '';
    public bool $isProcessing = false;
    public bool $showReceipt = false;
    public bool $paymentFailed = false;

    private const HOLIDAY_TEST_MODE = false;

    // APPLY_MODE: 'all'
    //             'single' 
    private const HOLIDAY_APPLY_MODE = 'all';

    // ── HOLIDAY promo state ────────────────────────────────────────────────────
    public bool $holidayEligible = false;
    public bool $holidayApplied  = false;

    // ── MERDEKA promo config (backend only, TIDAK bisa diubah dari FE) ─────────
    private const MERDEKA_TEST_MODE           = false; // true = paksa aktif, buat test
    private const MERDEKA_DATE                = '2026-08-17';
    private const MERDEKA_EXTRA_PRINT_ADDON   = 7;     // addon id extra print
    private const MERDEKA_EXTRA_PRINT_PRICE   = 17000; // harga promo/pcs
    private const MERDEKA_KEYCHAIN_ADDON      = 8;     // addon id keychain
    private const MERDEKA_KEYCHAIN_PAIR_PRICE = 8000;  // harga per 2 pcs
    private const MERDEKA_FRAME_PRODUCT       = 1;     // Frame 4 gaya 2 lembar
    private const MERDEKA_FRAME_PRICE         = 45000;
    private const MERDEKA_HIGHANGLE_PRODUCT   = 4;     // wajib item ini biar extra print bisa 17rb

    public bool $merdekaEligible = false;
    public bool $merdekaApplied  = false;

    // ── DOUBLE EXPERIENCE (Gandaria City only) ──────────────────────────────────
    private const DOUBLEEXP_OUTLET          = 'Gandaria City'; // cocokin sama value di DB
    private const DOUBLEEXP_HIGHANGLE       = 4;
    private const DOUBLEEXP_BIGROOM         = [11, 12];
    private const DOUBLEEXP_BUNDLE_PRICE    = 135000;

    public bool $doubleExpEligible = false;
    public bool $doubleExpApplied  = false;

    // ── PAYLESS PRINT MORE (all outlet) ─────────────────────────────────────────
    private const PAYLESS_HIGHANGLE     = 4;
    private const PAYLESS_EXTRA_ADDON   = 7;
    private const PAYLESS_DISCOUNT_UNIT = 5000;

    public bool $paylessEligible = false;
    public bool $paylessApplied  = false;

    protected $listeners = ['regenerate' => 'puppet'];

    public function validateCoupon()
    {
        // Block manual coupon entry if HOLIDAY / MERDEKA already applied
        if ($this->holidayApplied) {
            $this->addError('coupon', 'Promo Holiday sudah aktif, tidak bisa digabung dengan promo lain.');
            return;
        }
        if ($this->merdekaApplied) {
            $this->addError('coupon', 'Promo Merdeka sudah aktif, tidak bisa digabung dengan promo lain.');
            return;
        }
        if ($this->doubleExpApplied || $this->paylessApplied) {
            $this->addError('coupon', 'Promo sedang aktif, tidak bisa digabung dengan promo lain.');
            return;
        }

        $discount = App\Models\promo::where('code', $this->couponchck)->first();
        if ($discount) {
            $this->coupon = $discount;
            session()->flash('successvc', 'voucher ditambahkan!.');
        } else {
            $this->addError('coupon', 'Voucher Tidak Ditemukan!');
        }
    }

    // ── HOLIDAY: auto-check whenever the name field changes (also called on mount) ──
    public function updatedName(): void
    {
        $this->checkHolidayEligibility();
    }

    private function checkHolidayEligibility(): void
    {
        $now = Carbon::now();

        if (self::HOLIDAY_TEST_MODE) {
            $isValidDay  = true;
            $isValidTime = true;
        } else {
            // Senin (1) s/d Kamis (4)
            $isValidDay = in_array((int) $now->dayOfWeekIso, [1, 2, 3, 4]);

            // Jam 11:00 - 15:00
            $isValidTime = $now->format('H:i') >= '11:00' && $now->format('H:i') <= '15:00';
        }

        // Order harus mengandung minimal 1 Standar Frame (product_id 1, 2, atau 3)
        $hasStandarFrame = $this->order
            ? $this->order->items->contains(fn($it) => in_array((int) $it->product_id, [1, 2, 3]))
            : false;

        $this->holidayEligible = $isValidDay && $isValidTime && $hasStandarFrame;
    }

    // ── HOLIDAY: apply the promo ───────────────────────────────────────────────
    // All Standar Frame items (product_id 1, 2, 3) get priced at Rp 40.000 each.
    public function applyHoliday(): void
    {
        $now = Carbon::now();

        if (!self::HOLIDAY_TEST_MODE) {
            // Re-validate day (Senin s/d Kamis)
            if (!in_array((int) $now->dayOfWeekIso, [1, 2, 3, 4])) {
                $this->addError('coupon', 'Promo Holiday hanya berlaku Senin s/d Kamis.');
                return;
            }

            // Re-validate time (11:00 - 15:00)
            if (!($now->format('H:i') >= '11:00' && $now->format('H:i') <= '15:00')) {
                $this->addError('coupon', 'Promo Holiday hanya berlaku jam 11:00 - 15:00.');
                return;
            }
        }

        // Block stacking with other promos
        if ($this->coupon) {
            $this->addError('coupon', 'Promo Holiday tidak dapat digabungkan dengan promo lain.');
            return;
        }

        // Fetch HOLIDAY promo row
        $promo = \App\Models\promo::where('code', 'HOLIDAY')->first();
        if (!$promo) {
            $this->addError('coupon', 'Promo Holiday belum dikonfigurasi di sistem.');
            return;
        }

        // Find all Standar Frame items (product_id 1, 2, 3)
        $standarItems = $this->order->items->filter(
            fn($it) => in_array((int) $it->product_id, [1, 2, 3])
        );

        if ($standarItems->isEmpty()) {
            $this->addError('coupon', 'Promo Holiday hanya berlaku untuk Standar Frame.');
            return;
        }

        // APPLY_MODE 'single' → cuma item Standar Frame pertama yg kena potong
        if (self::HOLIDAY_APPLY_MODE === 'single') {
            $standarItems = $standarItems->take(1);
        }
        // APPLY_MODE 'all' → biarin semua item Standar Frame kena potong (default)

        // Discount = sum of (original price - 40.000) for every qualifying item,
        // only counted when the original price is above 40.000.
        $holidayPrice = 40000;
        $discount     = $standarItems->sum(function ($item) use ($holidayPrice) {
            $price = (float) $item->product->price;
            return $price > $holidayPrice ? ($price - $holidayPrice) : 0;
        });

        if ($discount <= 0) {
            $this->addError('coupon', 'Produk sudah di harga promo atau lebih rendah.');
            return;
        }

        $promo->diskon = $discount;

        $this->coupon          = $promo;
        $this->holidayApplied  = true;

        session()->flash('successvc', '🎉 Promo Holiday diterapkan! Standar Frame Rp 40.000.');
        $this->refreshData();
    }

    private function checkMerdekaEligibility(): void
    {
        $this->merdekaEligible = self::MERDEKA_TEST_MODE
            || Carbon::now()->isSameDay(Carbon::parse(self::MERDEKA_DATE));
    }

    // ── MERDEKA: apply the promo ───────────────────────────────────────────────
    // Nyalain mode harga spesial. Diskon dihitung ulang tiap ada perubahan cart
    // lewat recomputeMerdekaDiscount() (di addAddon/increaseAddon/decreaseAddon),
    // jadi tetep akurat walau item ditambah belakangan.
    public function applyMerdeka(): void
    {
        if (!self::MERDEKA_TEST_MODE && !Carbon::now()->isSameDay(Carbon::parse(self::MERDEKA_DATE))) {
            $this->addError('coupon', 'Promo Merdeka hanya berlaku 17 Agustus 2026.');
            return;
        }

        // Block stacking dengan promo lain
        if ($this->coupon) {
            $this->addError('coupon', 'Promo Merdeka tidak dapat digabungkan dengan promo lain.');
            return;
        }

        $promo = \App\Models\promo::where('code', 'MERDEKA')->first();
        if (!$promo) {
            $this->addError('coupon', 'Promo Merdeka belum dikonfigurasi di sistem.');
            return;
        }

        $promo->diskon         = 0; // dihitung ulang di bawah
        $this->coupon          = $promo;
        $this->merdekaApplied  = true;

        $this->recomputeMerdekaDiscount();
        $this->recomputePaylessDiscount();

        session()->flash('successvc', '🎉 Promo Merdeka diterapkan!');
        $this->refreshData();
    }

    // Hitung ulang diskon MERDEKA berdasarkan isi cart SEKARANG.
    // Dipanggil tiap ada perubahan addon/qty selama merdekaApplied = true,
    // jadi tetep akurat walau item ditambah belakangan (case 2 & 3 di brief).
    private function recomputeMerdekaDiscount(): void
    {
        if (!$this->merdekaApplied || !$this->coupon) {
            return;
        }

        $items = OrderItem::where('order_id', $this->id)->get();

        // ── 1) Frame 4 gaya 2 Lembar (product_id 1) → Rp 45.000 ────────────────
        $frameItems     = $items->where('product_id', self::MERDEKA_FRAME_PRODUCT);
        $frameDiscount  = $frameItems->sum(function ($it) {
            $price = (float) ($it->product->price ?? 0);
            return $price > self::MERDEKA_FRAME_PRICE ? ($price - self::MERDEKA_FRAME_PRICE) : 0;
        });

        // ── 2) Extra Print 17rb, HANYA yg nempel di item High Angle (id 4) ─────
        $highAngleIds  = $items->where('product_id', self::MERDEKA_HIGHANGLE_PRODUCT)->pluck('id');
        $printAddons   = OrderItemAddon::whereIn('order_item_id', $highAngleIds)
            ->where('addon_id', self::MERDEKA_EXTRA_PRINT_ADDON)
            ->get();
        $printQty         = (int) $printAddons->sum('qty');
        $printNormalPrice = (float) (Product::find(self::MERDEKA_EXTRA_PRINT_ADDON)->price ?? 0);
        $printDiscount    = $printQty * max(0, $printNormalPrice - self::MERDEKA_EXTRA_PRINT_PRICE);

        // ── 3) Keychain: 2 pcs = Rp 8.000 (ganjil sisanya harga normal) ────────
        $keyAddons = OrderItemAddon::whereIn('order_item_id', $items->pluck('id'))
            ->where('addon_id', self::MERDEKA_KEYCHAIN_ADDON)
            ->get();
        $keyQty         = (int) $keyAddons->sum('qty');
        $keyNormalPrice = (float) (Product::find(self::MERDEKA_KEYCHAIN_ADDON)->price ?? 0);
        $keyNormalTotal = $keyQty * $keyNormalPrice;
        $pairs          = intdiv($keyQty, 2);
        $remainder      = $keyQty % 2;
        $keyPromoTotal  = ($pairs * self::MERDEKA_KEYCHAIN_PAIR_PRICE) + ($remainder * $keyNormalPrice);
        $keyDiscount    = max(0, $keyNormalTotal - $keyPromoTotal);

        $this->coupon->diskon = $frameDiscount + $printDiscount + $keyDiscount;
    }

    // ── DOUBLE EXPERIENCE ────────────────────────────────────────────────────────
    private function checkDoubleExpEligibility(): void
    {
        $isOutlet = strtolower(trim(auth()->user()->outlet ?? '')) === strtolower(self::DOUBLEEXP_OUTLET);

        $hasHighAngle = $this->order
            ? $this->order->items->contains(fn($it) => (int) $it->product_id === self::DOUBLEEXP_HIGHANGLE)
            : false;

        $hasBigRoom = $this->order
            ? $this->order->items->contains(fn($it) => in_array((int) $it->product_id, self::DOUBLEEXP_BIGROOM))
            : false;

        $this->doubleExpEligible = $isOutlet && $hasHighAngle && $hasBigRoom;
    }

    public function applyDoubleExp(): void
    {
        if (strtolower(trim(auth()->user()->outlet ?? '')) !== strtolower(self::DOUBLEEXP_OUTLET)) {
            $this->addError('coupon', 'Promo Double Experience cuma berlaku di outlet Gandaria City.');
            return;
        }

        if ($this->coupon) {
            $this->addError('coupon', 'Promo tidak dapat digabungkan dengan promo lain.');
            return;
        }

        $highAngleItem = $this->order->items->first(fn($it) => (int) $it->product_id === self::DOUBLEEXP_HIGHANGLE);
        $bigRoomItem   = $this->order->items->first(fn($it) => in_array((int) $it->product_id, self::DOUBLEEXP_BIGROOM));

        if (!$highAngleItem || !$bigRoomItem) {
            $this->addError('coupon', 'Wajib beli 1 High Angle + 1 BigRoom bareng buat promo ini.');
            return;
        }

        $promo = \App\Models\promo::where('code', 'DOUBLEEXP')->first();
        if (!$promo) {
            $this->addError('coupon', 'Promo Double Experience belum dikonfigurasi di sistem.');
            return;
        }

        $original = (float) $highAngleItem->product->price + (float) $bigRoomItem->product->price;
        $discount = $original > self::DOUBLEEXP_BUNDLE_PRICE ? ($original - self::DOUBLEEXP_BUNDLE_PRICE) : 0;

        if ($discount <= 0) {
            $this->addError('coupon', 'Harga udah di bawah harga bundle.');
            return;
        }

        $promo->diskon        = $discount;
        $this->coupon          = $promo;
        $this->doubleExpApplied = true;

        session()->flash('successvc', '🎉 Promo Double Experience diterapkan! High Angle + BigRoom Rp 135.000.');
        $this->refreshData();
    }

    // ── PAYLESS PRINT MORE ───────────────────────────────────────────────────────
    private function checkPaylessEligibility(): void
    {
        $this->paylessEligible = $this->order
            ? $this->order->items->contains(fn($it) => (int) $it->product_id === self::PAYLESS_HIGHANGLE)
            : false;
    }

    public function applyPayless(): void
    {
        if ($this->coupon) {
            $this->addError('coupon', 'Promo tidak dapat digabungkan dengan promo lain.');
            return;
        }

        $hasHighAngle = $this->order->items->contains(fn($it) => (int) $it->product_id === self::PAYLESS_HIGHANGLE);
        if (!$hasHighAngle) {
            $this->addError('coupon', 'Promo ini wajib ada minimal 1 High Angle di order.');
            return;
        }

        $promo = \App\Models\promo::where('code', 'PAYLESS')->first();
        if (!$promo) {
            $this->addError('coupon', 'Promo Payless belum dikonfigurasi di sistem.');
            return;
        }

        $promo->diskon        = 0; // dihitung ulang di bawah
        $this->coupon          = $promo;
        $this->paylessApplied  = true;

        $this->recomputePaylessDiscount();

        session()->flash('successvc', '🎉 Promo Payless Print More diterapkan!');
        $this->refreshData();
    }

    // Sama kayak MERDEKA: dihitung ulang tiap addon Extra Print berubah,
    // jadi tetep akurat walau extra print ditambah/dikurang belakangan.
    private function recomputePaylessDiscount(): void
    {
        if (!$this->paylessApplied || !$this->coupon) {
            return;
        }

        $highAngleIds = OrderItem::where('order_id', $this->id)
            ->where('product_id', self::PAYLESS_HIGHANGLE)
            ->pluck('id');

        $printQty = (int) OrderItemAddon::whereIn('order_item_id', $highAngleIds)
            ->where('addon_id', self::PAYLESS_EXTRA_ADDON)
            ->sum('qty');

        $this->coupon->diskon = $printQty * self::PAYLESS_DISCOUNT_UNIT;
    }

    public function addcustomer($orderId)
    {
        $this->isProcessing = true;
        $this->paymentFailed = false;

        // Pastiin diskon MERDEKA fresh sebelum disimpen ke order
        $this->recomputeMerdekaDiscount();
        $this->recomputePaylessDiscount();

        if (! $this->puppet($orderId)) {
            $this->isProcessing = false;
            $this->paymentFailed = true;
            $this->addError('payment', 'Kode transaksi tidak dapat dibuat sekarang. Coba lagi sebentar lagi.');
            return;
        }

        $customer = customer::create($this->only(['name', 'telp', 'email', 'instagram']));

        Order::find($orderId)->update([
            'customer_id' => $customer->id,
            'status'      => 'paid',
            'apprcode'    => $this->appr,
            'total'       => $this->total - ($this->coupon->diskon ?? 0),
            'promo_id'    => ($this->coupon->id ?? 0),
            'outlet'      => auth()->user()->outlet,
        ]);

        $this->refreshData();

        return redirect()->route('checkout', ['id' => $orderId, 'showReceipt' => 1, 'sync' => 1]);
    }

    #[On('regenerate')]
    public function puppet($orderId)
    {
        $cartItems = OrderItem::where('order_id', $orderId)->get();
        $orders    = [];
        $OrderItem = OrderItem::where('order_id', $orderId)->first();

        foreach ($cartItems as $item) {
            $orders[] = [
                'OrderId' => $item->id,
                'type'    => $item->printType,
                'count' => ($item->product_id == 11 || $item->product_id == 12)
    ? $item->printCount + 1
    : $item->printCount,
                'kode'    => 'FREE',
            ];
        }

        $servers = [
            'http://api.neophotoindonesia.my.id/generate',
            'https://blokmhighmerah.neophotoindonesia.my.id/generate',
            'http://backup2.neophotoindonesia.my.id/generate',
        ];

        $maxRetry = 1;
        $data     = [];
        $generated = false;
        
        if($OrderItem->printType == 111){
            foreach ($data as $result) {
            $item = $cartItems->where('id', $result['idx'])->first();
            if ($item && !empty("000000000")) {
                $item->code = "000000000";
                $item->save();
            }
        }}else{
        foreach ($servers as $server) {
            $attempt = 0;

            do {
                $attempt++;

                try {
                    $res = Http::connectTimeout(10)
                        ->timeout(10)
                        ->post($server, ['orders' => $orders]);
                } catch (\Exception $e) {
                    continue;
                }

                if (!$res->successful()) {
                    continue;
                }

                $responseData = $res->json();
                $data = is_array($responseData) && array_is_list($responseData)
                    ? $responseData
                    : [$responseData];
                $valid = collect($data)->filter(
                    fn($item) => is_array($item) && !empty($item['kupon'])
                )->count();

                if ($valid > 0) {
                    $generated = true;
                    break 2;
                }
            } while ($attempt < $maxRetry);
        }

        foreach ($data as $result) {
            $item = $cartItems->where('id', $result['idx'])->first();
            if ($item && !empty($result['kupon'])) {
                $item->code = $result['kupon'];
                $item->save();
            }
        }

        return $generated;
    }}

    public function mount($id)
    {
        $this->showReceipt = request()->boolean('showReceipt');

        if (auth()->user()->outlet == "superadmin") {
            $this->name = "Test";
            $this->appr = "123123";
            $this->telp = 123123;
        }
        $this->id    = $id;
        $this->order = Order::with(['items.product', 'items.addons.addon'])->find($id);
        $this->appr  = $this->order->apprcode;

        // Wipe any leftover addons from a previous session (page refresh handling).
        // Only do this if the order hasn't been paid yet — never touch completed orders.
        $this->resetOrderAddons();

        // Reload after wipe so the rest of mount() sees a clean slate
        $this->order = Order::with(['items.product', 'items.addons.addon'])->find($id);

        $this->orderItem = OrderItem::where('order_id', $this->id)->get();

        $this->orderAddon = OrderItemAddon::whereIn(
            'order_item_id',
            $this->orderItem->pluck('id')
        )->get();

        // Run initial HOLIDAY + MERDEKA + promo Gandaria/Payless check
        $this->checkHolidayEligibility();
        $this->checkMerdekaEligibility();
        $this->checkDoubleExpEligibility();
        $this->checkPaylessEligibility();
    }

    private function resetOrderAddons(): void
    {
        // Safety: never touch orders that are already paid
        if (!$this->order || $this->order->status === 'paid') {
            return;
        }

        $items = OrderItem::where('order_id', $this->id)->get();

        foreach ($items as $item) {
            $addons = OrderItemAddon::where('order_item_id', $item->id)->get();

            // Reverse printCount adjustments from print-affecting addons (ids 6 & 7)
            // so we restore the original base printCount for each item.
            foreach ($addons as $addon) {
                if (in_array($addon->addon_id, [6, 7])) {
                    $perUnit = ($item->product_id == 1) ? 2 : 1;
                    $item->printCount -= $perUnit * (int) $addon->qty;
                }
            }
            $item->save();

            // Wipe all addons for this item
            OrderItemAddon::where('order_item_id', $item->id)->delete();
        }

        // Also reset any in-memory promo/coupon state from a prior session
        $this->coupon         = '';
        $this->couponchck     = '';
        $this->holidayApplied  = false;
        $this->merdekaApplied  = false;
        $this->doubleExpApplied = false;
        $this->paylessApplied   = false;
    }

    public function addAddon($orderItemId, $addonId)
    {
        $orderItem = OrderItem::find($orderItemId);
        $addon     = OrderItemAddon::where('order_item_id', $orderItemId)
            ->where('addon_id', $addonId)
            ->first();

        $affectsPrint = in_array($addonId, [6, 7]);

        if ($addon) {
            if ($affectsPrint) {
                $orderItem->printCount += ($orderItem->product_id == 1) ? 2 : 1;
                $orderItem->save();
            }
            $addon->qty += 1;
            $addon->save();
        } else {
            $newAddon = OrderItemAddon::create([
                'order_item_id' => $orderItemId,
                'addon_id'      => $addonId,
                'qty'           => 0,
            ]);

            if ($affectsPrint) {
                $orderItem->printCount += ($orderItem->product_id == 1) ? 2 : 1;
                $orderItem->save();
            }
            $newAddon->qty += 1;
            $newAddon->save();
        }

        $this->recomputeMerdekaDiscount();
        $this->recomputePaylessDiscount();
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->order     = Order::with('items.product')->find($this->id);
        $this->orderItem = OrderItem::where('order_id', $this->id)->get();

        $this->orderAddon = OrderItemAddon::whereIn(
            'order_item_id',
            $this->orderItem->pluck('id')
        )->get();

        $this->appr = Order::find($this->id)->apprcode;
    }

    public function getTotalProperty()
    {
        $productTotal = $this->order->items->sum(fn($item) => $item->product->price);

        $addonTotal = $this->orderAddon->sum(function ($addon) {
            $product = Product::find($addon->addon_id);
            return $product->price * $addon->qty;
        });

        return $productTotal + $addonTotal;
    }

    public function increaseAddon($orderItemId, $addonId)
    {
        $orderItem = OrderItem::find($orderItemId);
        $addon     = OrderItemAddon::firstOrCreate(
            ['order_item_id' => $orderItemId, 'addon_id' => $addonId],
            ['qty' => 0]
        );

        if (in_array($addonId, [6, 7])) {
            $orderItem->printCount += ($orderItem->product_id == 1) ? 2 : 1;
            $orderItem->save();
        }

        $addon->qty += 1;
        $addon->save();

        $this->recomputeMerdekaDiscount();
        $this->recomputePaylessDiscount();
        $this->refreshData();
    }

    public function getAddonQty($orderItemId, $addonId)
    {
        $addon = $this->orderAddon
            ->where('order_item_id', $orderItemId)
            ->where('addon_id', $addonId)
            ->first();

        return $addon ? $addon->qty : 0;
    }

    public function decreaseAddon($orderItemId, $addonId)
    {
        $orderItem = OrderItem::find($orderItemId);
        $addon     = OrderItemAddon::where('order_item_id', $orderItemId)
            ->where('addon_id', $addonId)
            ->first();

        if (!$addon) {
            return;
        }

        if ($addon->id == 6 || $addon->id == 7) {
            if ($orderItem->product_id == 1) {
                $orderItem->printCount -= 2;
            } else {
                $orderItem->printCount -= 1;
            }
            $orderItem->save();
            $addon->qty -= 1;
            $addon->save();
        }

        if ($addon->qty > 1) {
            $addon->qty -= 1;
            $addon->save();
        } else {
            $addon->delete();
        }

        $this->recomputeMerdekaDiscount();
        $this->recomputePaylessDiscount();
        $this->refreshData();
    }
};
?>

<div>
    @include('sweetalert2::index')

    <form
        class="text-center text-white needs-validation"
        style="background:#ffd5e8"
        wire:submit.prevent="addcustomer({{ $order->id }})"
    >
        <div class="d-flex justify-content-start" style="position:sticky;top:0;">
            <a href="/" class="btn btn-primary mt-4 mx-4 fs-3 rounded-pill px-1 fw-bold border-4"
               style="background-color:#ffb3ba;border-color:#d6eadf;width:10rem">
                Back <i class="fa-solid text-white fa-left-long"></i>
            </a>
        </div>

        <div class="container-fluid d-flex justify-content-around align-items-center"
             style="background:#ffd5e8;height:50vw;">

            {{-- Contact Information --}}
            <div class="col-6 py-5 px-5"
                 style="border-radius:9%;background-color:#fab7be;box-shadow:17px 10px 15px -3px rgba(0,0,0,0.1);">
                <h3 class="text-white fw-bold fs-1">Contact Information</h3>

                <div class="mb-3 text-white text-start fs-4">
                    <label class="form-label text-white fw-bold">Nama</label>
                    <input type="text" class="form-control" name="name" wire:model.live.debounce.400ms="name" required />
                    <div class="invalid-feedback">Isi Nama Kamu!</div>
                </div>

                {{-- ── MERDEKA banner ─────────────────────────────────────────── --}}
                @if ($merdekaEligible && !$this->coupon && !$this->appr)
                    <div class="alert border-0 text-start py-2 mb-2"
                         style="background:linear-gradient(135deg,#c1121f,#780000);color:#fff;border-radius:14px;text-shadow:0 1px 3px rgba(0,0,0,0.5);">
                        <div class="fw-bold mb-1" style="font-size:15px;">🇮🇩 PROMO MERDEKA (17-8-45)</div>
                        <ul class="mb-2 ps-3" style="font-size:12px;line-height:1.4;">
                            <li>Wajib follow IG neophotoindonesia</li>
                            <li>Extra Print High Angle Rp 17.000 (wajib udah punya item High Angle)</li>
                            <li>2 Keychain Rp 8.000, no minimal transaksi</li>
                            <li>Frame 4 Gaya 2 Lembar Rp 45.000</li>
                            <li>Gak bisa gabung promo lain, bukan paket bundling</li>
                        </ul>
                        <button type="button"
                                wire:click="applyMerdeka"
                                class="btn btn-sm fw-bold rounded-pill px-3"
                                style="background:#fff;color:#780000;text-shadow:none;">
                            Klaim Merdeka
                        </button>
                        @error('coupon')
                            <div class="mt-2" style="font-size:12px;color:#ffd6d6;">⚠ {{ $message }}</div>
                        @enderror
                    </div>
                @endif

                @if ($merdekaApplied)
                    <div class="alert border-0 text-start py-2 mb-2"
                         style="background:#d1f7c4;color:#1a7a4a;border-radius:14px;">
                        ✅ <strong>Promo Merdeka aktif</strong> — harga khusus otomatis kehitung di total.
                    </div>
                @endif

                {{-- ── DOUBLE EXPERIENCE (Gandaria City only) ────────────────── --}}
                @if ($doubleExpEligible && !$this->coupon && !$this->appr)
                    <div class="alert border-0 text-start py-2 mb-2"
                         style="background:linear-gradient(135deg,#7209b7,#3a0ca3);color:#fff;border-radius:14px;">
                        <div class="fw-bold mb-1" style="font-size:14px;">✨ DOUBLE EXPERIENCE</div>
                        <div style="font-size:12px;">High Angle + BigRoom bareng = Rp 135.000</div>
                        <button type="button"
                                wire:click="applyDoubleExp"
                                class="btn btn-sm fw-bold rounded-pill px-3 mt-2"
                                style="background:#fff;color:#3a0ca3;">
                            Klaim Double Experience
                        </button>
                        @error('coupon')
                            <div class="mt-2" style="font-size:12px;color:#ffd6ff;">⚠ {{ $message }}</div>
                        @enderror
                    </div>
                @endif
                @if ($doubleExpApplied)
                    <div class="alert border-0 text-start py-2 mb-2"
                         style="background:#d1f7c4;color:#1a7a4a;border-radius:14px;">
                        ✅ <strong>Double Experience aktif</strong> — High Angle + BigRoom Rp 135.000.
                    </div>
                @endif

                {{-- ── PAYLESS PRINT MORE ─────────────────────────────────────── --}}
                @if ($paylessEligible && !$this->coupon && !$this->appr)
                    <div class="alert border-0 text-start py-2 mb-2"
                         style="background:linear-gradient(135deg,#2a9d8f,#264653);color:#fff;border-radius:14px;">
                        <div class="fw-bold mb-1" style="font-size:14px;">🖨️ PAYLESS PRINT MORE</div>
                        <div style="font-size:12px;">Extra Print High Angle potong Rp 5.000/pcs</div>
                        <button type="button"
                                wire:click="applyPayless"
                                class="btn btn-sm fw-bold rounded-pill px-3 mt-2"
                                style="background:#fff;color:#264653;">
                            Klaim Payless
                        </button>
                        @error('coupon')
                            <div class="mt-2" style="font-size:12px;color:#d3f8f2;">⚠ {{ $message }}</div>
                        @enderror
                    </div>
                @endif
                @if ($paylessApplied)
                    <div class="alert border-0 text-start py-2 mb-2"
                         style="background:#d1f7c4;color:#1a7a4a;border-radius:14px;">
                        ✅ <strong>Payless aktif</strong> — Extra Print High Angle potong Rp 5.000/pcs.
                    </div>
                @endif

                <div class="mb-3 text-white text-start fs-4">
                    <label class="form-label text-white fw-bold">Telpon</label>
                    <input type="number" class="form-control" name="telp" wire:model="telp" required />
                    <input class="form-check-input" disabled checked type="checkbox" />
                    <label class="form-check-label text-white text-start fs-5">
                        Beritahu saya tentang promo dan update terbaru
                    </label>
                </div>

                <div class="mb-3 text-white text-start fs-4">
                    <label class="form-label text-white fw-bold">Email</label>
                    <input type="email" class="form-control" name="email" wire:model="email" />
                </div>

                <div class="mb-3 text-white text-start fs-4">
                    <label class="form-label text-white fw-bold">IG</label>
                    <input type="text" class="form-control" name="instagram" wire:model="instagram" />
                </div>
            </div>

            {{-- Order Detail --}}
            <div class="col-4 h-75 px-2 py-3"
                 style="overflow-y:scroll;border-radius:9%;background-color:#fab7be;box-shadow:17px 10px 15px -3px rgba(0,0,0,0.1);">
                <h3 class="text-white fw-bold fs-1">Detail Pesanan</h3>

                <div class="container">
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        @foreach ($order->items as $item)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button style="background-color:#fab7be"
                                            class="accordion-button d-flex justify-content-between fs-5 fw-bold text-white show"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#flush-{{ $item->product->type }}"
                                            aria-expanded="false">
                                        {{ $item->product->name }}'s Addon
                                    </button>
                                </h2>
                                <div id="flush-{{ $item->product->type }}"
                                     class="accordion-collapse collapse"
                                     wire:ignore.self
                                     data-bs-parent="#accordionFlushExample">
                                    <div style="background-color:#fab7be"
                                         class="accordion-body text-white d-flex flex-column">
                                        @php $addon = collect(); @endphp

                                        @if ($item->product->id == 1)
                                            @php $addon = \App\Models\Product::whereIn('id',[6,8,9])->get(); @endphp
                                        @elseif ($item->product->id == 2 || $item->product->id == 3)
                                            @php $addon = \App\Models\Product::whereIn('id',[6])->get(); @endphp
                                        @elseif (in_array($item->product->id, [4, 5, 11, 12]))
                                            @php $addon = \App\Models\Product::whereIn('id',[7])->get(); @endphp
                                        @endif

                                        @foreach ($addon as $add)
                                            @php
                                                // ── MERDEKA: label harga khusus ───────────────────
                                                $merdekaPrintNow = $merdekaApplied
                                                    && $add->id == 7
                                                    && (int) $item->product->id === 4;
                                                $merdekaKeyNow = $merdekaApplied && $add->id == 8;
                                            @endphp
                                            <div class="d-flex align-items-end justify-content-between">
                                                <button type="button"
                                                        wire:loading.class="disabled"
                                                        wire:click="addAddon({{ $item->id }},{{ $add->id }})"
                                                        class="btn my-1 btn-outline-success">
                                                    {{ $add->name }}
                                                    @if ($merdekaPrintNow)
                                                        <span class="badge bg-danger ms-1">Merdeka 17rb</span>
                                                    @elseif ($merdekaKeyNow)
                                                        <span class="badge bg-danger ms-1">Merdeka 2pcs/8rb</span>
                                                    @endif
                                                </button>
                                                @if ($add->price * $this->getAddonQty($item->id, $add->id))
                                                    <p class="text-white fs-5 fw-bold">
                                                        Rp {{ number_format($add->price * $this->getAddonQty($item->id, $add->id), 0, ',', '.') }}
                                                    </p>
                                                @else
                                                    <p class="text-white fs-5 fw-bold">
                                                        Rp {{ number_format($add->price, 0, ',', '.') }}
                                                    </p>
                                                @endif
                                            </div>

                                            @if ($this->getAddonQty($item->id, $add->id) > 0)
                                                <div class="d-flex align-items-end justify-content-end">
                                                    <button type="button"
                                                            wire:loading.class="disabled"
                                                            wire:click="decreaseAddon({{ $item->id }},{{ $add->id }})"
                                                            class="btn my-2 mx-1 btn-outline-success">-</button>
                                                    <input type="number"
                                                           class="btn my-2 text-center fw-bold mx-1 btn-outline-success"
                                                           value="{{ $this->getAddonQty($item->id, $add->id) }}"
                                                           readonly />
                                                    <button type="button"
                                                            wire:click="increaseAddon({{ $item->id }},{{ $add->id }})"
                                                            class="btn my-2 mx-1 btn-outline-success">+</button>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-between border-top border-2 border-white align-items-center">
                    <p class="text-white fw-bold fs-4">Total</p>
                    <p class="text-white fw-bold fs-4">
                        Rp {{ number_format($this->total - ($this->coupon->diskon ?? 0), 0, ',', '.') }}
                    </p>
                    <input type="text" wire:model="paid" class="d-none" value="{{ $this->total }}" name="total" />
                </div>

                <div class="d-flex justify-content-end my-1 text-white">
                    <a data-bs-toggle="modal" data-bs-target="#staticBackdrop"
                       class="btn btn-primary fs-5 rounded-pill px-3 fw-bold border-4"
                       style="background-color:#ffb3ba;border-color:#d6eadf;">
                        pay <i class="text-white fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                {{-- Payment Modal --}}
                 <div class="modal fade {{ $showReceipt ? 'show' : '' }}"
                     wire:ignore.self
                     id="staticBackdrop"
                     data-bs-backdrop="static"
                     data-bs-keyboard="false"
                     tabindex="-1"
                     aria-labelledby="staticBackdropLabel"
                     aria-hidden="{{ $showReceipt ? 'false' : 'true' }}"
                     @if ($showReceipt) style="display:block;" @endif>
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                    @if ($this->appr)
                                        Informasi Pembayaran
                                    @else
                                        Konfirmasi Pembayaran
                                    @endif
                                </h1>
                                {{-- Only show close button when NOT processing --}}
                                <button type="button"
                                        class="btn-close"
                                        {{ $isProcessing ? 'disabled' : '' }}
                                        data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>

                            <div class="modal-body">

                                {{-- ── Spinner: d-none by default, shown ONLY while addcustomer runs ── --}}
                                <div wire:loading.class.remove="d-none"
                                     wire:target="addcustomer"
                                     class="d-none flex-column align-items-center justify-content-center py-5 gap-3">
                                    <div class="spinner-border text-primary"
                                         style="width:3.5rem;height:3.5rem;" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="fw-bold text-primary mb-0 fs-5">Memproses pembayaran...</p>
                                    <small class="text-muted">Mohon jangan tutup halaman ini</small>
                                </div>

                                {{-- ── Receipt: visible by default, hidden while addcustomer runs ── --}}
                                <div wire:loading.class="d-none" wire:target="addcustomer">
                                    <div class="card p-4">
                                        <h3 class="fw-bold mb-3">Receipt</h3>

                                        @error('payment')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror

                                        {{-- HOLIDAY banner inside modal too --}}
                                        @if ($holidayApplied)
                                            <div class="alert border-0 text-start py-2 mb-3"
                                                 style="background:linear-gradient(135deg,#ff6ec7,#a64ac9);color:#fff;border-radius:10px;">
                                                🎉 <strong>Promo Holiday</strong> aktif — Standar Frame Rp 40.000
                                            </div>
                                        @endif

                                        {{-- MERDEKA banner inside modal too --}}
                                        @if ($merdekaApplied)
                                            <div class="alert border-0 text-start py-2 mb-3"
                                                 style="background:linear-gradient(135deg,#c1121f,#780000);color:#fff;border-radius:10px;text-shadow:0 1px 3px rgba(0,0,0,0.5);">
                                                🇮🇩 <strong>Promo Merdeka</strong> aktif — harga khusus 17-8-45
                                            </div>
                                        @endif

                                        @if ($doubleExpApplied)
                                            <div class="alert border-0 text-start py-2 mb-3"
                                                 style="background:linear-gradient(135deg,#7209b7,#3a0ca3);color:#fff;border-radius:10px;">
                                                ✨ <strong>Double Experience</strong> aktif — High Angle + BigRoom Rp 135.000
                                            </div>
                                        @endif

                                        @if ($paylessApplied)
                                            <div class="alert border-0 text-start py-2 mb-3"
                                                 style="background:linear-gradient(135deg,#2a9d8f,#264653);color:#fff;border-radius:10px;">
                                                🖨️ <strong>Payless Print More</strong> aktif — Extra Print Rp 5.000/pcs off
                                            </div>
                                        @endif

                                        {{-- Coupon input (only when not yet paid AND no promo active) --}}
                                        @if (!$this->appr && !$holidayApplied && !$merdekaApplied && !$doubleExpApplied && !$paylessApplied)
                                            <div class="input-group mb-3">
                                                <input type="text"
                                                       class="form-control"
                                                       wire:model="couponchck"
                                                       name="couponchck"
                                                       placeholder="Have a Coupon?" />
                                                <button class="btn btn-outline-success"
                                                        wire:click="validateCoupon()"
                                                        type="button">Verify</button>
                                            </div>

                                            @if (session()->has('successvc'))
                                                <div class="alert alert-success">{{ session('successvc') }}</div>
                                            @endif

                                            @error('coupon')
                                                <span class="error text-danger">{{ $message }}</span>
                                            @enderror
                                        @endif

                                        {{-- Items --}}
                                        @php $total = 0; @endphp

                                        @foreach ($order->items as $item)
                                            <div class="border-bottom pb-2">
                                                <div class="d-flex justify-content-between fw-bold">
                                                    <span>{{ $item->product->name }}</span>
                                                    @if (!$this->appr)
                                                        <span>Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
                                                        @php $total += $item->product->price; @endphp
                                                    @endif
                                                </div>

                                                @foreach ($item->addons as $addon)
                                                    <div class="d-flex justify-content-between ms-3 text-muted">
                                                        <span>+ {{ $addon->addon->name }} (x{{ $addon->qty }})</span>
                                                        @if (!$this->appr)
                                                            <span>Rp {{ number_format($addon->addon->price * $addon->qty, 0, ',', '.') }}</span>
                                                            @php $total += $addon->addon->price * $addon->qty; @endphp
                                                        @endif
                                                    </div>
                                                @endforeach

                                                <div class="d-flex align-items-center justify-content-between border-bottom my-2 border-top text-muted">
                                                    <span>CODE:</span>
                                                    <span class="fs-5 fw-bold">
                                                        @php
                                                            $original  = $item->code;
                                                            $formatted = implode('-', str_split($original, 3));
                                                        @endphp
                                                        {{ $formatted ?? 'Finish payment' }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach

                                        {{-- Discount + Total (unpaid state) --}}
                                        @if ($paymentFailed)
                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                <a href="{{ route('home') }}" class="btn btn-secondary">Cancel</a>
                                                <button type="button"
                                                        class="btn btn-primary"
                                                        wire:click="addcustomer({{ $order->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="addcustomer">
                                                    <span wire:loading wire:target="addcustomer" class="spinner-border spinner-border-sm me-1"></span>
                                                    Retry
                                                </button>
                                            </div>

                                        @elseif (!$this->appr)
                                            @if ($this->coupon)
                                                <div class="d-flex justify-content-between ms-3 text-muted">
                                                    <span>DISCOUNT{{ $holidayApplied ? ' (HOLIDAY)' : ($merdekaApplied ? ' (MERDEKA)' : ($doubleExpApplied ? ' (DOUBLE EXP)' : ($paylessApplied ? ' (PAYLESS)' : ''))) }}:</span>
                                                    <span>- Rp {{ number_format($this->coupon->diskon ?? 0, 0, ',', '.') }}</span>
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between ms-3 text-muted">
                                                <span>Total:</span>
                                                <span>Rp {{ number_format($this->total - ($this->coupon->diskon ?? 0), 0, ',', '.') }}</span>
                                            </div>

                                            <div class="mb-3 text-start mt-2 fs-4">
                                                <input type="text"
                                                       class="form-control"
                                                       placeholder="Approval Code Kasir"
                                                       name="appr"
                                                       wire:model="appr"
                                                       required />
                                            </div>

                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button"
                                                        class="btn btn-secondary"
                                                        {{ $isProcessing ? 'disabled' : '' }}
                                                        data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit"
                                                        class="btn btn-primary"
                                                        {{ $isProcessing ? 'disabled' : '' }}>
                                                    @if ($isProcessing)
                                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                                        Memproses...
                                                    @else
                                                        Bayar
                                                    @endif
                                                </button>
                                            </div>

                                        {{-- Paid state: show finish button --}}
                                        @else
                                            <div class="mb-3 text-start mt-2 fs-4">
                                                <input type="text"
                                                       class="form-control"
                                                       placeholder="Approval Code Kasir"
                                                       name="appr"
                                                       wire:model="appr"
                                                       required />
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <a href="/" class="btn btn-primary">Finish</a>
                                            </div>
                                        @endif

                                    </div>{{-- /.card --}}
                                </div>{{-- /.receipt wrapper --}}

                            </div>{{-- /.modal-body --}}
                        </div>{{-- /.modal-content --}}
                    </div>{{-- /.modal-dialog --}}
                </div>{{-- /.modal --}}

            </div>{{-- /.order detail col --}}
        </div>{{-- /.container-fluid --}}
    </form>
</div>