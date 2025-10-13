<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
  </head>
  <body class="bg-gray-200 flex justify-center items-start py-5">
    <!-- A4 Paper Container -->
    <div class="bg-white w-[210mm] max-w-full aspect-[210/297] p-6 shadow-lg flex flex-col">
      <!-- Header -->
      <div class="bg-pink-200 p-4">
        <div class="flex justify-between items-end">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Suemi Online Shop</h1>
            <p class="text-xs text-gray-600 mt-1 leading-tight">
              BLK 9 L5 Calliandra 2 Phase 1 Greenwoods<br>
              Executive Village Paliparan 1 Dasmariñas Cavite<br>
              facebook.com/suemishop | 09151772074
            </p>
          </div>
          <div class="text-3xl font-bold text-gray-800 tracking-wider">
            P A Y S L I P
          </div>
        </div>
      </div>
      <!-- Dark Gray Line under Header -->
      <div class=" bg-gray-800 h-[0.3in]"></div>

      <!-- Net Pay -->
      <p class="text-2xl text-right mr-5 mt-2 font-bold">
        <span class="font-semibold">Net Pay:</span> ₱<span>100</span>
      </p>

        <p class="text-1xl text-left ml-3 my-2 font-bold"><span class="font-semibold">Employee Details:</span></p>
<div class="flex gap-8 mb-6 w-full">
      <!-- Employee Info -->
      <div class="grid grid-cols-2 gap-0 border border-gray-200 shadow-md rounded-none bg-white w-3/4 text-sm">
        <div class="bg-gray-100 px-2 py-1 border-b border-gray-300">Employee Name:</div>
        <div class="px-2 py-1 border-b border-gray-300">Claire</div>
        <div class="bg-gray-100 px-2 py-1 border-b border-gray-300">Email:</div>
        <div class="px-2 py-1 border-b border-gray-300">claire@gmail.com</div>
        <div class="bg-gray-100 px-2 py-1 border-b border-gray-300">Contact No:</div>
        <div class="px-2 py-1 border-b border-gray-300">09918895966</div>
        <div class="bg-gray-100 px-2 py-1 border-b border-gray-300">SSS No.:</div>
        <div class="px-2 py-1 border-b border-gray-300">sss</div>
        <div class="bg-gray-100 px-2 py-1 border-b border-gray-300">Philhealth No.:</div>
        <div class="px-2 py-1 border-b border-gray-300">none</div>
        <div class="bg-gray-100 px-2 py-1 border-b border-gray-300">Pag-IBIG No.:</div>
        <div class="px-2 py-1 border-b border-gray-300">none</div>
      </div>
  <!-- Salary Info -->
      <div class="grid grid-cols-2 gap-0 border border-gray-200 shadow-md rounded-none bg-white w-1/4 text-sm">
        <div class="bg-gray-100 px-2 py-1 border-b border-gray-300 font-semibold">Designation:</div>
        <div class="px-2 py-1 border-b border-gray-300">Admin assistant</div>
        <div class="bg-gray-100 px-2 py-1 border-b border-gray-300 font-semibold">Pay Period (Start):</div>
        <div class="px-2 py-1 border-b border-gray-300">October 1, 2025</div>
        <div class="bg-gray-100 px-2 py-1 border-b border-gray-300 font-semibold">Pay Period (End):</div>
        <div class="px-2 py-1 border-b border-gray-300">October 15, 2025</div>
      </div>
    </div>

     <!-- Earnings & Deductions -->
    <div class="grid grid-cols-2 gap-4">

      <!-- Earnings Table -->
<div>
  <table class="w-full border border-gray-300 text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="border px-2 py-1">TOTAL OVERTIME/HOURS WORKED</th>
        <th class="border px-2 py-1">TOTAL DAYS WORKED</th>
        <th class="border px-2 py-1">HOURLY RATE</th>
        <th class="border px-2 py-1">DAILY RATE</th>
        <th class="border px-2 py-1">EARNINGS</th>
        <th class="border px-2 py-1">TOTAL</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="border text-center px-2 py-1">10</td>
        <td class="border text-center px-2 py-1">11</td>
        <td class="border text-center px-2 py-1">12</td>
        <td class="border text-center px-2 py-1">₱13</td>
        <td class="border px-2 py-1">Total Daily Pay</td>
        <td class="border px-2 py-1" >
          ₱14
        </td>
      </tr>
      <tr>
        <td class="border text-center px-2 py-1" colspan="4"></td>
        <td class="border px-2 py-1">Total Overtime Pay</td>
        <td class="border px-2 py-1" id="totalOvertimePay">
          ₱15
        </td>
      </tr>
      <tr>
        <td class="border text-center px-2 py-1" colspan="4"></td>
        <td class="border px-2 py-1">Total Commission Pay</td>
        <td class="border px-2 py-1" id="totalCommissionPay">₱0.00</td>
      </tr>
    </tbody>
    <tfoot>
      <tr class="font-semibold">
        <td colspan="4" class="border px-2 py-1"></td>
        <td class="border text-left px-2 py-1 text-sm">Gross Salary</td>
        <td class="border text-left px-2 py-1 text-sm">
          ₱16
        </td>
      </tr>
    </tfoot>
  </table>

  <!-- Add Commission Button -->
  <button id="addCommissionBtn" class="mt-2 bg-rose-500 hover:bg-rose-500 text-white px-4 py-2 rounded no-print">
    Add Commission
  </button>
</div>

<!-- Commission Modal -->
<div id="commissionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center no-print">
  <div class="bg-white p-6 rounded shadow-lg w-96">
    <h2 class="text-lg font-bold mb-4">Add Commission</h2>
    <label class="block mb-2">Quantity</label>
    <input type="number" id="commissionQty" class="w-full border px-2 py-1 mb-4" />
    <label class="block mb-2">Amount per Unit</label>
    <input type="number" id="commissionAmt" class="w-full border px-2 py-1 mb-4" />
    <div class="flex justify-end gap-2">
      <button id="cancelCommission" class="px-4 py-2 border rounded">Cancel</button>
      <button id="saveCommission" class="px-4 py-2 bg-rose-500 text-white rounded">OK</button>
    </div>
  </div>
</div>


<!-- Deductions Table -->
      <div class="relative">
        <table id="deductionsTable" class="w-full border border-gray-300 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="border px-2 py-1">DEDUCTIONS</th>
              <th class="border px-2 py-1">AMOUNT</th>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot>
            <tr class="font-semibold">
              <td class="border px-2 py-1">Total Deductions</td>
              <td class="border px-2 py-1" id="totalDeductions">0</td>
            </tr>
            <tr class="font-bold">
              <td class="border px-2 py-1 text-right">NET Salary</td>
              <td class="border px-2 py-1" id="netSalary">100</td>
            </tr>
          </tfoot>
        </table>


        <button id="addDeductionBtn" class="absolute right-0 mt-2 bg-rose-500 hover:bg-rose-500 text-white px-4 py-2 rounded no-print">
          Add Deduction
        </button>
      </div>
    </div>

     <!-- Deduction Modal -->
    <div id="deductionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center no-print">
      <div class="bg-white p-6 rounded shadow-lg w-96">
        <h2 class="text-lg font-bold mb-4">Add Deduction</h2>
        <label class="block mb-2">Description</label>
        <input type="text" id="deductionDesc" class="w-full border px-2 py-1 mb-4" />
        <label class="block mb-2">Amount</label>
        <input type="number" id="deductionAmount" class="w-full border px-2 py-1 mb-4" />
        <div class="flex justify-end gap-2">
          <button id="cancelDeduction" class="px-4 py-2 border rounded">Cancel</button>
          <button id="saveDeduction" class="px-4 py-2 bg-rose-500 text-white rounded">OK</button>
        </div>
      </div>
    </div>

<!-- Signatures -->
<!-- Signatures -->
<div class="grid grid-cols-3 mt-20 text-center text-sm text-gray-600">
  <!-- Employee -->
  <div class="flex flex-col items-center">
    <img src="{{ asset('images/signature.png') }}" alt="Employee Signature" class="h-11 mb-0">
    <span class="font-bold underline leading-tight">____CLAIRE NAKILA____</span>
    <span class="text-xs mt-0">Employee Overprinted Name</span>
  </div>

  <!-- Prepared By -->
  <div class="flex flex-col items-center">
    <img src="{{ asset('images/signature.png') }}" alt="Prepared By Signature" class="h-11 mb-0">
    <span class="font-bold underline leading-tight">____CLAIRE NAKILA____</span>
    <span class="text-xs mt-0">Prepared By</span>
  </div>

  <!-- Employer -->
  <div class="flex flex-col items-center">
    <img src="{{ asset('images/signature.png') }}" alt="Employer Signature" class="h-11 mb-0">
    <span class="font-bold underline leading-tight">____CLAIRE NAKILA____</span>
    <span class="text-xs mt-0">Employer Overprinted Name</span>
  </div>
</div>




    </div>
  </body>
</html>
