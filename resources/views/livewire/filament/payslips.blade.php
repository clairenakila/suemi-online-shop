<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @vite('resources/css/app.css')
</head>
<body class="bg-gray-200 flex justify-center items-start py-5">
  <!-- Bondpaper Container -->
  <div class="bg-white w-full max-w-[800px] p-2 shadow-lg flex flex-col space-y-4">

    <!-- Repeat Payslip Twice -->
    @for ($i = 0; $i < 2; $i++)
    <div class="border border-gray-300 p-2 md:p-4 flex flex-col text-xs md:text-sm">
      <!-- Header -->
      <div class="bg-pink-200 p-2 flex justify-between items-end">
        <div>
          <h1 class="text-sm md:text-base font-bold text-gray-800">Suemi Online Shop</h1>
          <p class="text-[9px] md:text-xs text-gray-600 leading-tight">
            BLK 9 L5 Calliandra 2 Phase 1 Greenwoods<br>
            Executive Village Paliparan 1 Dasmariñas Cavite<br>
            facebook.com/suemishop | 09151772074
          </p>
        </div>
        <div class="text-lg md:text-xl font-bold text-gray-800 tracking-wider">
          P A Y S L I P
        </div>
      </div>
      <div class="bg-gray-800 h-[2px] my-1"></div>

      <!-- Gross / Deductions / Net Pay -->
      <div class="text-right space-y-0.5 mr-1">
        <p class="font-semibold"><span>Gross Pay:</span> ₱1000</p>
        <p class="font-semibold text-red-600"><span>Deductions:</span> - ₱100</p>
        <p class="font-bold text-lg"><span>Net Pay:</span> ₱100</p>
      </div>

      <!-- Employee & Salary Details -->
      <div class="flex flex-col md:flex-row gap-2 mt-2">
        <!-- Employee Info -->
        <div class="grid grid-cols-2 border border-gray-200 text-xs w-full md:w-3/4">
          <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Name:</div>
          <div class="px-1 py-0.5 border-b border-gray-300">{{ $user->name ?? 'N/A' }}</div>
          <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Email:</div>
          <div class="px-1 py-0.5 border-b border-gray-300">{{ $user->email ?? 'N/A' }}</div>
          <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Contact Number:</div>
          <div class="px-1 py-0.5 border-b border-gray-300">{{ $user->contact_number ?? 'N/A' }}</div>
          <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">SSS No.:</div>
          <div class="px-1 py-0.5 border-b border-gray-300">{{ $user->sss_number ?? 'N/A' }}</div>
          <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Philhealth No.:</div>
          <div class="px-1 py-0.5 border-b border-gray-300">{{ $user->philhealth_number ?? 'N/A' }}</div>
          <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Pag-IBIG No.:</div>
          <div class="px-1 py-0.5 border-b border-gray-300">{{ $user->pagibig_number ?? 'N/A' }}</div>
        </div>
        <!-- Salary Info -->
        <div class="grid grid-cols-2 border border-gray-200 text-xs w-full md:w-1/4">
          <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Designation:</div>
          <div class="px-1 py-0.5 border-b border-gray-300">{{ $user->role->name ?? 'N/A' }}</div>
          <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Pay Period (Start):</div>
          <div class="px-1 py-0.5 border-b border-gray-300">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') ?? 'N/A' }}</div>
          <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Pay Period (End):</div>
          <div class="px-1 py-0.5 border-b border-gray-300">{{ \Carbon\Carbon::parse($endDate)->format('M d, Y') ?? 'N/A' }}</div>
        </div>
      </div>

      <!-- Earnings & Deductions -->
      <div class="flex flex-col md:flex-row gap-2 mt-1">
        <!-- Earnings -->
        <div class="w-full md:w-1/2 overflow-x-auto">
          <table class="w-full border border-gray-300 text-xs">
            <thead class="bg-gray-100">
              <tr>
                <th class="border px-1 py-0.5">TOTAL OVERTIME/HOURS WORKED</th>
                <th class="border px-1 py-0.5">TOTAL DAYS WORKED</th>
                <th class="border px-1 py-0.5">HOURLY RATE</th>
                <th class="border px-1 py-0.5">DAILY RATE</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="border text-center px-1 py-0.5">{{ $totalHours }}</td>
                <td class="border text-center px-1 py-0.5">{{ $totalDays }}</td>
                <td class="border text-center px-1 py-0.5"> {{ $user->hourly_rate !== null ? '₱' . number_format($user->hourly_rate) : 'N/A' }}</td>
                <td class="border text-center px-1 py-0.5">{{ $user->daily_rate !== null ? '₱' . number_format($user->daily_rate) : 'N/A' }}</td>
              </tr>
              <tr>
                <td colspan="4" class="border px-1 py-0.5">
                  Total Daily Pay = ₱100<br>
                  Total Overtime = ₱100<br>
                  Commission: 11 items * ₱12 = ₱1200
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="font-bold text-left">
                <td colspan="4" class="border px-1 py-0.5">Gross Pay: ₱1200</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Deductions -->
        <div class="w-full md:w-1/2 overflow-x-auto">
          <table class="w-full border border-gray-300 text-xs">
            <thead class="bg-gray-100">
              <tr><th class="border px-1 py-0.5 text-left">DEDUCTIONS</th></tr>
            </thead>
            <tbody>
              <tr><td class="border px-1 py-0.5">Cash Advance = ₱100<br>Cellphone = ₱1000</td></tr>
            </tbody>
            <tfoot>
              <tr class="font-bold"><td class="border px-1 py-0.5">Total Deductions: ₱100</td></tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Signatures -->
      <div class="grid grid-cols-3 mt-2 text-center text-xs gap-1">
        <div class="flex flex-col items-center">
          <img src="{{ asset('images/signature.png') }}" class="h-6 mb-0">
          <span class="font-bold underline">CLAIRE NAKILA</span>
          <span>Employee</span>
        </div>
        <div class="flex flex-col items-center">
          <img src="{{ asset('images/signature.png') }}" class="h-6 mb-0">
          <span class="font-bold underline">CLAIRE NAKILA</span>
          <span>Prepared By</span>
        </div>
        <div class="flex flex-col items-center">
          <img src="{{ asset('images/signature.png') }}" class="h-6 mb-0">
          <span class="font-bold underline">CLAIRE NAKILA</span>
          <span>Employer</span>
        </div>
      </div>
    </div>
    @endfor
  </div>
</body>
</html>
