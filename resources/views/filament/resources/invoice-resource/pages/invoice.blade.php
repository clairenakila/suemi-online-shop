<x-filament-panels::page class="overflow-auto p-4 bg-gray-50">

  <!-- Bondpaper Container -->
  <div class="bg-white w-full max-w-[800px] p-2 shadow-lg flex flex-col space-y-4 mx-auto">

    <!-- Repeat Payslip Twice -->
    @for ($i = 0; $i < 2; $i++)
      <div class="border border-gray-300 p-2 md:p-4 flex flex-col text-xs md:text-sm">

        <!-- Header -->
        <div class="bg-pink-200 p-2 flex justify-between items-end">
          <div>
            <h1 class="text-sm md:text-base font-bold text-gray-800">Suemi Online Shop</h1>
            <p class="text-[9px] md:text-xs text-gray-600 leading-tight">
              BLK 9 L5 Calliandra 2 Phase 2 Greenwoods<br>
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
          <p class="font-semibold">Gross Pay: ₱15,500.00</p>
          <p class="font-semibold text-red-600">Deductions: ₱2,000.00</p>
          <p class="font-bold text-lg">NET Pay: ₱13,500.00</p>
        </div>

        <!-- Employee & Salary Details -->
        <div class="flex flex-col md:flex-row gap-2 mt-2">
          <!-- Employee Info -->
          <div class="grid grid-cols-2 border border-gray-200 text-xs w-full md:w-3/4">
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Name:</div>
            <div class="px-1 py-0.5 border-b border-gray-300">Juan Dela Cruz</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Email:</div>
            <div class="px-1 py-0.5 border-b border-gray-300">juan@example.com</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Contact Number:</div>
            <div class="px-1 py-0.5 border-b border-gray-300">09171234567</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">SSS No.:</div>
            <div class="px-1 py-0.5 border-b border-gray-300">12-3456789-0</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Philhealth No.:</div>
            <div class="px-1 py-0.5 border-b border-gray-300">1234567890</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Pag-IBIG No.:</div>
            <div class="px-1 py-0.5 border-b border-gray-300">9876543210</div>
          </div>

          <!-- Salary Info -->
          <div class="grid grid-cols-2 border border-gray-200 text-xs w-full md:w-1/4">
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Designation:</div>
            <div class="px-1 py-0.5 border-b border-gray-300">Staff</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Pay Period (Start):</div>
            <div class="px-1 py-0.5 border-b border-gray-300">Oct 01, 2025</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Pay Period (End):</div>
            <div class="px-1 py-0.5 border-b border-gray-300">Oct 15, 2025</div>
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
                  <td class="border text-center px-1 py-0.5">10</td>
                  <td class="border text-center px-1 py-0.5">12</td>
                  <td class="border text-center px-1 py-0.5">₱100.00</td>
                  <td class="border text-center px-1 py-0.5">₱500.00</td>
                </tr>
                <tr>
                  <td colspan="4" class="border px-1 py-0.5">
                    Total Daily Pay = ₱6,000.00<br>
                    Total Overtime Pay = ₱1,000.00<br>
                    Commission:<br>
                    5 pcs. Widget A * ₱200.00 each = ₱1,000.00<br>
                    Total Commission: ₱1,000.00
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="font-bold text-left">
                  <td colspan="4" class="border px-1 py-0.5">Gross Pay: ₱15,500.00</td>
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
                <tr>
                  <td class="border px-1 py-0.5">
                    SSS: ₱500.00<br>
                    Philhealth: ₱500.00<br>
                    Pag-IBIG: ₱500.00<br>
                    Tax: ₱500.00
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="font-bold">
                  <td class="border px-1 py-0.5">Total Deductions: ₱2,000.00</td>
                </tr>
              </tfoot>
            </table>
          </div>

        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-3 mt-2 text-center text-xs gap-1">
          <div class="flex flex-col items-center">
            <img src="" class="h-10 mb-0">
            <span class="font-bold underline uppercase">Juan Dela Cruz</span>
            <span>Employee Overprinted Name</span>
          </div>
          <div class="flex flex-col items-center">
            <img src="{{ asset('images/sue_signature.png') }}" class="h-10 mb-0">
            <span class="font-bold underline">SUE LAPIDEZ</span>
            <span>Prepared By</span>
          </div>
          <div class="flex flex-col items-center">
            <img src="{{ asset('images/michael_signature.png') }}" class="h-10 mb-0">
            <span class="font-bold underline">MICHAEL LAPIDEZ</span>
            <span>Employer Overprinted Name</span>
          </div>
        </div>

      </div>
    @endfor
  </div>

  <!-- Print Button -->
  <button 
    class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg hover:bg-green-300 transition"
    onclick="printPayslip()">
    🖨️ Print Payslip
  </button>

  <script>
    function printPayslip() {
      document.querySelector('button').classList.add('hidden');
      setTimeout(() => { window.print(); }, 300);
      window.onafterprint = () => document.querySelector('button').classList.remove('hidden');

      // Optional: download PDF
      html2pdf().from(document.body).set({
        margin: 10,
        filename: 'Static_Payslip.pdf',
        html2canvas: { scale: 2 },
        jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
      }).save();
    }
  </script>

</x-filament-panels::page>
