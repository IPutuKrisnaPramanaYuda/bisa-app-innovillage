<div class="max-w-md mx-auto mt-20 bg-white p-8 rounded shadow text-center">
    <h2 class="text-2xl font-bold mb-4">Pembayaran Manual</h2>
    <p class="mb-4">Total Tagihan Anda:</p>
    <div class="text-3xl font-bold text-orange-600 mb-6">Rp {{ number_format($totalTagihan) }}</div>

    <div class="bg-gray-100 p-4 rounded mb-6 text-left">
        <p class="font-bold">Transfer ke Bank BRI:</p>
        <p class="text-xl">1234-5678-9000-000</p>
        <p>a.n BUMDES BENGKALA MAJU</p>
    </div>

    <form action="{{ route('payment.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="block mb-2 text-sm font-bold text-gray-700">Upload Bukti Transfer</label>
        <input type="file" name="proof" required class="w-full mb-4 border p-2 rounded">
        <button type="submit" class="w-full bg-green-600 text-white py-3 rounded font-bold hover:bg-green-700">
            Konfirmasi Pembayaran
        </button>
    </form>
</div>  