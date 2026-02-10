<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  @vite('resources/css/app.css')
</head>
<body class="bg-white flex justify-center items-start py-5">

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
          <p class="font-semibold">Gross Pay: 
            <span id="grossPay{{ $i }}">
              {{ '₱' . number_format(
                  ($totalDays * ($user->daily_rate ?? 0)) + 
                  ($totalHours * ($user->hourly_rate ?? 0)) + 
                  ($totalCommission ?? 0),2
              ) }}
          </span>

          </p>
          <p class="font-semibold text-red-600">
          (Deductions: ₱<span id="totalDeductionTop{{ $i }}">0.00</span>)
          
          <p class="font-bold text-lg">NET Pay:
           
        <span id="netPay{{ $i }}">
  ₱{{ number_format(
    (($totalDays * ($user->daily_rate ?? 0)) + 
    ($totalHours * ($user->hourly_rate ?? 0)) + 
    ($totalCommission ?? 0)) - 0, 2
  ) }}
</span>
        </p>
        </div>

        <!-- Employee & Salary Details -->
        <div class="flex flex-col md:flex-row gap-2 mt-2">
          <!-- Employee Info -->
          <div class="grid grid-cols-2 border border-gray-200 text-xs w-full md:w-3/4">
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Name:</div>
            <div class="px-1 py-0.5 border-b border-gray-300 break-words whitespace-normal">{{ $user->name ?? 'N/A' }}</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Email:</div>
            <div class="px-1 py-0.5 border-b border-gray-300 break-words whitespace-normal">{{ $user->email ?? 'N/A' }}</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Contact Number:</div>
            <div class="px-1 py-0.5 border-b border-gray-300 break-words whitespace-normal">{{ $user->contact_number ?? 'N/A' }}</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">SSS No.:</div>
            <div class="px-1 py-0.5 border-b border-gray-300 break-words whitespace-normal">{{ $user->sss_number ?? 'N/A' }}</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Philhealth No.:</div>
            <div class="px-1 py-0.5 border-b border-gray-300 break-words whitespace-normal">{{ $user->philhealth_number ?? 'N/A' }}</div>
            <div class="bg-gray-100 px-1 py-0.5 border-b border-gray-300">Pag-IBIG No.:</div>
            <div class="px-1 py-0.5 border-b border-gray-300 break-words whitespace-normal">{{ $user->pagibig_number ?? 'N/A' }}</div>
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
                  <td class="border text-center px-1 py-0.5">{{ $totalHours ?? 0 }}</td>
                  <td class="border text-center px-1 py-0.5">{{ $totalDays ?? 0 }}</td>
                  <td class="border text-center px-1 py-0.5">{{ $user->hourly_rate !== null ? '₱' . number_format($user->hourly_rate,2) : 'N/A' }}</td>
                  <td class="border text-center px-1 py-0.5">{{ $user->daily_rate !== null ? '₱' . number_format($user->daily_rate,2) : 'N/A' }}</td>
                </tr>
                <tr>
                  <td colspan="4" class="border px-1 py-0.5">
                    Total Daily Pay = {{ $user->daily_rate !== null ? '₱' . number_format($totalDays * $user->daily_rate, 2) : 'N/A' }}<br>
                    Total Overtime Pay = {{ $user->hourly_rate !== null ? '₱' . number_format($totalHours * $user->hourly_rate, 2) : 'N/A' }}<br>
                    Commission:
                    <div id="commissionList{{ $i }}" class=" ml-5">
                    @if(count($commissions) > 0)
                        @foreach($commissions as $c)
                            {{ $c['quantity'] }} pcs. {{ $c['description'] }} * ₱{{ number_format($c['price'], 2) }} each = ₱{{ number_format($c['total'], 2) }}<br>
                        @endforeach
                    @else
                        N/A
                    @endif
                </div>
                    Total Commission: ₱<span id="totalCommission{{ $i }}">{{ isset($totalCommission) ? number_format($totalCommission,2) : '0.00' }} </span> 
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="font-bold text-left">
                  <td colspan="4" class="border px-1 py-0.5">
                    Gross Pay: 
                   <span id="grossPayTable{{ $i }}">
              {{ '₱' . number_format(
                  ($totalDays * ($user->daily_rate ?? 0)) + 
                  ($totalHours * ($user->hourly_rate ?? 0)) + 
                  ($totalCommission ?? 0),2
              ) }}
          </span>

                  </td>
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
                  <td id="deductionTable{{ $i }}"  class="border px-1 py-0.5">
                     N/A
                </td></tr>
              </tbody>
              <tfoot>
                <tr class="font-bold">
              <td class="border px-1 py-0.5">Total Deductions: ₱<span id="totalDeduction{{ $i }}">0.00</span></td>
              </tfoot>
            </table>
          </div>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-3 mt-2 text-center text-xs gap-1">
          <div class="flex flex-col items-center">
            <img src="" class="h-10 mb-0">
            <span class="font-bold underline uppercase">{{ $user->name ?? 'N/A' }}</span>
            <span>Employee Overprinted Name</span>
          </div>
          <div class="flex flex-col items-center">
            <img class="h-10 mb-0">
            <span class="font-bold underline">SUE LAPIDEZ</span>
            <span>Prepared By</span>
          </div>
          <div class="flex flex-col items-center">
          <!-- <img src="{{ asset('images/michael_signature.png') }}" class="h-10 mb-0"> -->

            <img  class="h-10 mb-0">
            <span class="font-bold underline">MICHAEL LAPIDEZ</span>
            <span>Employer Overprinted Name</span>
          </div>
        </div>
      </div>
    @endfor
  <!-- </div> -->

  <!-- Floating Add Commission Button -->
  <button 
    class="fixed top-5 right-5 bg-amber-500 text-white px-4 py-2 rounded shadow-lg z-50 hover:bg-amber-300 transition"
    onclick="showCommissionModal()">
    + Add Commission
  </button>

  <!-- Commission Modal -->
  <div id="commissionModal" class="fixed inset-0 bg-white bg-opacity-10 hidden z-50">
    <div class="flex justify-center items-center h-full">
      <form id="commissionForm" class="bg-white w-full max-w-md p-4 rounded shadow-lg relative" onsubmit="addCommission(event)">
        <button type="button" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800" onclick="hideCommissionModal()">✕</button>
        <h2 class="text-lg font-bold mb-2">Add Commission</h2>
        <div class="flex flex-col space-y-2">
          <input id="description" type="text" placeholder="Description" class="border px-2 py-1 rounded" required>
          <input id="quantity" type="number" placeholder="Quantity" class="border px-2 py-1 rounded" required>
          <input id="price" type="number" placeholder="Price" class="border px-2 py-1 rounded" required>
        </div>
        <div class="flex justify-end mt-4 space-x-2">
          <button type="button" class="bg-gray-300 px-3 py-1 rounded" onclick="hideCommissionModal()">Cancel</button>
          <button type="submit" class="bg-amber-600 text-white px-3 py-1 rounded">OK</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Floating Add Deduction Button -->
<button 
  class="fixed top-16 right-5 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50 hover:bg-red-300 transition"
  onclick="showDeductionModal()">
  + Add Deduction
</button>

<!-- Deduction Modal -->
<div id="deductionModal" class="fixed inset-0 bg-white bg-opacity-10 hidden z-50">
  <div class="flex justify-center items-center h-full">
    <form id="deductionForm" class="bg-white w-full max-w-md p-4 rounded shadow-lg relative" onsubmit="addDeduction(event)">
      <button type="button" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800" onclick="hideDeductionModal()">✕</button>
      <h2 class="text-lg font-bold mb-2">Add Deduction</h2>
      <div class="flex flex-col space-y-2">
        <input id="description" type="text" placeholder="Description" class="border px-2 py-1 rounded" required>
        <input id="amount" type="number" placeholder="Amount" class="border px-2 py-1 rounded" required>
      </div>
      <div class="flex justify-end mt-4 space-x-2">
        <button type="button" class="bg-gray-300 px-3 py-1 rounded" onclick="hideDeductionModal()">Cancel</button>
        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">OK</button>
      </div>
    </form>
  </div>
</div>


<!-- Floating Print Payslip Button -->
<button 
  id="printButton"
  class="fixed top-28 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50 hover:bg-green-300 transition"
  onclick="handlePrintPayslip()">
  🖨️ Print Payslip
</button>

<!-- Floating Reset Button -->
<button 
  class="fixed top-40 right-5 bg-gray-500 text-white px-4 py-2 rounded shadow-lg z-50 hover:bg-gray-400 transition"
  onclick="resetPayslipData()">
  🧹 Reset Page
</button>

<!-- Floating Create Invoice Button -->
<button 
  id="createInvoiceButton"
  class="fixed top-52 right-5 bg-blue-600 text-white px-4 py-2 rounded shadow-lg z-50 hover:bg-blue-400 transition"
  onclick="handleCreateInvoice()">
  🧾 Create Invoice
</button>

  <script>
//   // ======================
//   // COMMISSION LOGIC
//   // ======================
//   let commissions = [];
//   let deductions = [];

//   function showCommissionModal() {
//     document.getElementById('commissionModal').classList.remove('hidden');
//   }

//   function hideCommissionModal() {
//     document.getElementById('commissionModal').classList.add('hidden');
//   }

//   function showDeductionModal() {
//     document.getElementById('deductionModal').classList.remove('hidden');
//   }

//   function hideDeductionModal() {
//     document.getElementById('deductionModal').classList.add('hidden');
//   }

//   // Shared computation for daily/overtime
//   const totalDailyPay = {{ $totalDays ?? 0 }} * {{ $user->daily_rate ?? 0 }};
//   const totalOvertimePay = {{ $totalHours ?? 0 }} * {{ $user->hourly_rate ?? 0 }};

//   // ======================
//   // Update All Displays
//   // ======================
//   function updateAll() {
//     let totalCom = commissions.reduce((sum, c) => sum + c.total, 0);
//     let totalDed = deductions.reduce((sum, d) => sum + d.amount, 0);
//     const grossPay = totalDailyPay + totalOvertimePay + totalCom;
//     const netPay = grossPay - totalDed;

//     for (let i = 0; i < 2; i++) {
//       // Update commission list
//       const list = document.getElementById('commissionList' + i);
//       list.innerHTML = commissions.length
//         ? commissions.map(c =>
//             `${c.quantity} pcs. ${c.description} x ₱${c.price.toFixed(2)} each = ₱${c.total.toFixed(2)}<br>`
//           ).join('')
//         : 'N/A';
//       document.getElementById('totalCommission' + i).innerText = totalCom.toFixed(2);

//       // Update gross pay (top and table)
//       document.getElementById('grossPay' + i).innerText =
//         '₱' + grossPay.toLocaleString('en-PH', { minimumFractionDigits: 2 });
//       document.getElementById('grossPayTable' + i).innerText =
//         '₱' + grossPay.toLocaleString('en-PH', { minimumFractionDigits: 2 });

//       // Update deductions table
//       const deductionTable = document.getElementById('deductionTable' + i);
//       deductionTable.innerHTML = deductions.length
//         ? deductions.map(d => `${d.description} = ₱${d.amount.toFixed(2)}<br>`).join('')
//         : 'N/A';

//       // Update total deductions (top and bottom)
//       document.getElementById('totalDeduction' + i).innerText = totalDed.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
//       document.getElementById('totalDeductionTop' + i).innerText = totalDed.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

//       // Update net pay dynamically
//       document.getElementById('netPay' + i).innerText =
//         '₱' + netPay.toLocaleString('en-PH', { minimumFractionDigits: 2 });
//     }
//   }

//   // ======================
//   // Add Commission
//   // ======================
//   function addCommission(e) {
//     e.preventDefault();
//     const description = document.getElementById('description').value;
//     const quantity = parseFloat(document.getElementById('quantity').value);
//     const price = parseFloat(document.getElementById('price').value);
//     const total = quantity * price;

//     commissions.push({ description, quantity, price, total });
//     updateAll();

//     document.getElementById('commissionForm').reset();
//     hideCommissionModal();
//   }

//   // ======================
//   // Add Deduction
//   // ======================
//   function addDeduction(e) {
//     e.preventDefault();
//     const description = document.querySelector('#deductionForm #description').value;
//     const amount = parseFloat(document.querySelector('#deductionForm #amount').value);

//     deductions.push({ description, amount });
//     updateAll();

//     document.getElementById('deductionForm').reset();
//     hideDeductionModal();
//   }

//    async function handlePrintPayslip() {
//   // Hide floating buttons before printing
//   document.querySelectorAll(
//     'button[onclick="showCommissionModal()"], button[onclick="showDeductionModal()"], #printButton'
//   ).forEach(btn => btn.classList.add('hidden'));

//   // Compute all totals
//   const totalDailyPay = {{ $totalDays ?? 0 }} * {{ $user->daily_rate ?? 0 }};
//   const totalOvertimePay = {{ $totalHours ?? 0 }} * {{ $user->hourly_rate ?? 0 }};
//   const totalCommission = commissions.reduce((sum, c) => sum + c.total, 0);
//   const commissionDescriptions = commissions.map(c => c.description).join(', ');
//   const commissionQuantity = commissions.reduce((sum, c) => sum + c.quantity, 0);
//   const totalDeduction = deductions.reduce((sum, d) => sum + d.amount, 0);
//   const deductionDescriptions = deductions.map(d => d.description).join(', ');
//   const grossPay = totalDailyPay + totalOvertimePay + totalCommission;
//   const netPay = grossPay - totalDeduction;

//   // Send to Laravel to create invoice record
//   await fetch("{{ route('invoices.create') }}", {
//   method: "POST",
//   headers: {
//     "Content-Type": "application/json",
//     "X-CSRF-TOKEN": "{{ csrf_token() }}",
//   },
//   body: JSON.stringify({
//     user_id: {{ $user->id }},
//     start_date: "{{ $startDate }}",
//     end_date: "{{ $endDate }}",
//     total_days: {{ $totalDays }},
//     total_hours: {{ $totalHours }},
//     total_daily_pay: totalDailyPay,
//     total_overtime_pay: totalOvertimePay,
//     total_commission: totalCommission,
//     commission_descriptions: commissionDescriptions,
//     commission_quantity: commissionQuantity,
//     total_deduction: totalDeduction,
//     deduction_descriptions: deductionDescriptions,
//     gross_pay: grossPay,
//     net_pay: netPay,
//   }),
// });

// console.log("✅ Invoice created!");


//   // Now print or download
//   setTimeout(() => {
//     window.print();
//   }, 500);
// }


//   // When print is done (or canceled), re-show buttons
//   window.onafterprint = () => {
//     document.querySelectorAll(
//       'button[onclick="showCommissionModal()"], button[onclick="showDeductionModal()"], #printButton'
//     ).forEach(btn => btn.classList.remove('hidden'));

//     // Optional: auto-download after print
//     downloadPayslipAsPDF();
//   };

//   // ======================
//   // AUTO-DOWNLOAD AS PDF
//   // ======================
// function downloadPayslipAsPDF() {
//   // Dynamic file name: EmployeeName_(StartDate_-_EndDate).pdf
//   const employeeName = "{{ str_replace(' ', '_', $user->name ?? 'Employee') }}";
//   const start = "{{ \Carbon\Carbon::parse($startDate)->format('M_d_Y') }}";
//   const end = "{{ \Carbon\Carbon::parse($endDate)->format('M_d_Y') }}";
//   const fileName = `${employeeName}_(${start}_-_ ${end}).pdf`;

//   const element = document.body;

//   const opt = {
//     margin: 0.2,
//     filename: fileName,
//     image: { type: 'jpeg', quality: 0.98 },
//     html2canvas: { scale: 2 },
//     jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
//   };

//   html2pdf().from(element).set(opt).save();
// }



//   // ======================
//   // ADD CTRL + P SHORTCUT
//   // ======================
//   window.addEventListener('keydown', function(e) {
//     if (e.ctrlKey && e.key.toLowerCase() === 'p') {
//       e.preventDefault(); // prevent default browser print
//       handlePrintPayslip();
//     }
//   });


  // ======================
// COMMISSION + DEDUCTION LOGIC WITH SMART RESET
// ======================
let commissions = [];
let deductions = [];

// 🧠 Load saved data on refresh
window.addEventListener('load', () => {
  const savedCommissions = localStorage.getItem('payslip_commissions');
  const savedDeductions = localStorage.getItem('payslip_deductions');

  if (savedCommissions) commissions = JSON.parse(savedCommissions);
  if (savedDeductions) deductions = JSON.parse(savedDeductions);

  updateAll();
});

// 🧹 Clear data when leaving payslip page (back to attendances or other pages)
window.addEventListener('beforeunload', (event) => {
  const nextUrl = document.activeElement?.href || '';
  const isNavigatingAway =
    nextUrl.includes('/attendances') || nextUrl.includes('/suemionlineshop');

  // ✅ Clear ONLY if going back to attendance or another page
  if (isNavigatingAway) {
    localStorage.removeItem('payslip_commissions');
    localStorage.removeItem('payslip_deductions');
  }
});


  function showCommissionModal() {
    document.getElementById('commissionModal').classList.remove('hidden');
  }

  function hideCommissionModal() {
    document.getElementById('commissionModal').classList.add('hidden');
  }

  function showDeductionModal() {
    document.getElementById('deductionModal').classList.remove('hidden');
  }

  function hideDeductionModal() {
    document.getElementById('deductionModal').classList.add('hidden');
  }

  // Shared computation for daily/overtime
  const totalDailyPay = {{ $totalDays ?? 0 }} * {{ $user->daily_rate ?? 0 }};
  const totalOvertimePay = {{ $totalHours ?? 0 }} * {{ $user->hourly_rate ?? 0 }};

  // ======================
  // Update All Displays
  // ======================
  function updateAll() {
    let totalCom = commissions.reduce((sum, c) => sum + c.total, 0);
    let totalDed = deductions.reduce((sum, d) => sum + d.amount, 0);
    const grossPay = totalDailyPay + totalOvertimePay + totalCom;
    const netPay = grossPay - totalDed;

    // 💾 Save to localStorage every update
    localStorage.setItem('payslip_commissions', JSON.stringify(commissions));
    localStorage.setItem('payslip_deductions', JSON.stringify(deductions));

    for (let i = 0; i < 2; i++) {
      // Update commission list
      const list = document.getElementById('commissionList' + i);
      list.innerHTML = commissions.length
        ? commissions.map(c =>
            `${c.quantity} pcs. ${c.description} x ₱${c.price.toFixed(2)} each = ₱${c.total.toFixed(2)}<br>`
          ).join('')
        : 'N/A';
      document.getElementById('totalCommission' + i).innerText = totalCom.toFixed(2);

      // Update gross pay (top and table)
      document.getElementById('grossPay' + i).innerText =
        '₱' + grossPay.toLocaleString('en-PH', { minimumFractionDigits: 2 });
      document.getElementById('grossPayTable' + i).innerText =
        '₱' + grossPay.toLocaleString('en-PH', { minimumFractionDigits: 2 });

      // Update deductions table
      const deductionTable = document.getElementById('deductionTable' + i);
      deductionTable.innerHTML = deductions.length
        ? deductions.map(d => `${d.description} = ₱${d.amount.toFixed(2)}<br>`).join('')
        : 'N/A';

      // Update total deductions (top and bottom)
      document.getElementById('totalDeduction' + i).innerText = totalDed.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      document.getElementById('totalDeductionTop' + i).innerText = totalDed.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

      // Update net pay dynamically
      document.getElementById('netPay' + i).innerText =
        '₱' + netPay.toLocaleString('en-PH', { minimumFractionDigits: 2 });
    }
  }

  // ======================
  // Add Commission
  // ======================
  function addCommission(e) {
    e.preventDefault();
    const description = document.getElementById('description').value;
    const quantity = parseFloat(document.getElementById('quantity').value);
    const price = parseFloat(document.getElementById('price').value);
    const total = quantity * price;

    commissions.push({ description, quantity, price, total });
    updateAll();

    document.getElementById('commissionForm').reset();
    hideCommissionModal();
  }

  // ======================
  // Add Deduction
  // ======================
  function addDeduction(e) {
    e.preventDefault();
    const description = document.querySelector('#deductionForm #description').value;
    const amount = parseFloat(document.querySelector('#deductionForm #amount').value);

    deductions.push({ description, amount });
    updateAll();

    document.getElementById('deductionForm').reset();
    hideDeductionModal();
  }

 // ======================
// PRINT PAYSLIP (NO INVOICE CREATION)
// ======================
async function handlePrintPayslip() {
  // Hide all floating buttons before printing
  document.querySelectorAll(
    'button[onclick="showCommissionModal()"], \
     button[onclick="showDeductionModal()"], \
     #printButton, \
     button[onclick="resetPayslipData()"], \
     #createInvoiceButton'
  ).forEach(btn => btn.classList.add('hidden'));

  // Compute totals (optional for console/debug)
  const totalDailyPay = {{ $totalDays ?? 0 }} * {{ $user->daily_rate ?? 0 }};
  const totalOvertimePay = {{ $totalHours ?? 0 }} * {{ $user->hourly_rate ?? 0 }};
  const totalCommission = commissions.reduce((sum, c) => sum + c.total, 0);
  const totalDeduction = deductions.reduce((sum, d) => sum + d.amount, 0);
  const grossPay = totalDailyPay + totalOvertimePay + totalCommission;
  const netPay = grossPay - totalDeduction;

  console.log("🖨 Printing payslip...");
  console.log({ totalDailyPay, totalOvertimePay, totalCommission, totalDeduction, grossPay, netPay });

  // Print
  setTimeout(() => {
    window.print();
  }, 300);
}

// When print is done (or canceled), re-show all buttons
window.onafterprint = () => {
  document.querySelectorAll(
    'button[onclick="showCommissionModal()"], \
     button[onclick="showDeductionModal()"], \
     #printButton, \
     button[onclick="resetPayslipData()"], \
     #createInvoiceButton'
  ).forEach(btn => btn.classList.remove('hidden'));

  // Auto-download as PDF after printing
  downloadPayslipAsPDF();
};

// ======================
// AUTO-DOWNLOAD AS PDF
// ======================
function downloadPayslipAsPDF() {
  const employeeName = "{{ str_replace(' ', '_', $user->name ?? 'Employee') }}";
  const start = "{{ \Carbon\Carbon::parse($startDate)->format('M_d_Y') }}";
  const end = "{{ \Carbon\Carbon::parse($endDate)->format('M_d_Y') }}";
  const fileName = `${employeeName}_(${start}_-_ ${end}).pdf`;

  const element = document.body;
  const opt = {
    margin: 0.2,
    filename: fileName,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
  };

  html2pdf().from(element).set(opt).save();
}

// ======================
// CTRL + P SHORTCUT
// ======================
window.addEventListener('keydown', function(e) {
  if (e.ctrlKey && e.key.toLowerCase() === 'p') {
    e.preventDefault(); // prevent default browser print
    handlePrintPayslip();
  }
});


// ======================
// RESET PAGE BUTTON LOGIC
// ======================
function resetPayslipData() {
  if (confirm("Are you sure you want to reset all commissions and deductions?")) {
    commissions = [];
    deductions = [];
    localStorage.removeItem('payslip_commissions');
    localStorage.removeItem('payslip_deductions');
    updateAll();
    alert("✅ Page has been reset!");
  }
}


// ======================
// CREATE INVOICE BUTTON LOGIC
// ======================
async function handleCreateInvoice() {
  // Compute all totals
  const totalDailyPay = {{ $totalDays ?? 0 }} * {{ $user->daily_rate ?? 0 }};
  const totalOvertimePay = {{ $totalHours ?? 0 }} * {{ $user->hourly_rate ?? 0 }};
  const totalCommission = commissions.reduce((sum, c) => sum + c.total, 0);
  
  // 🧾 Concatenate commission descriptions with (Quantity: , Price:)
  const commissionDescriptions = commissions
    .map(c => `${c.description} (Quantity: ${c.quantity}, Price: ₱${c.price.toFixed(2)})`)
    .join(', ');

  const commissionQuantity = commissions.reduce((sum, c) => sum + c.quantity, 0);

  const totalDeduction = deductions.reduce((sum, d) => sum + d.amount, 0);

  // 🧾 Concatenate deduction descriptions with (Amount:)
  const deductionDescriptions = deductions
    .map(d => `${d.description} (Amount: ₱${d.amount.toFixed(2)})`)
    .join(', ');

  const grossPay = totalDailyPay + totalOvertimePay + totalCommission;
  const netPay = grossPay - totalDeduction;

  try {
    const response = await fetch("{{ route('invoices.create') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": "{{ csrf_token() }}",
      },
      body: JSON.stringify({
        user_id: {{ $user->id }},
        start_date: "{{ $startDate }}",
        end_date: "{{ $endDate }}",
        total_days: {{ $totalDays }},
        total_hours: {{ $totalHours }},
        total_daily_pay: totalDailyPay,
        total_overtime_pay: totalOvertimePay,
        total_commission: totalCommission,
        commission_descriptions: commissionDescriptions,
        commission_quantity: commissionQuantity,
        total_deduction: totalDeduction,
        deduction_descriptions: deductionDescriptions,
        gross_pay: grossPay,
        net_pay: netPay,
      }),
    });

    if (response.ok) {
      alert("✅ Invoice successfully created!");
    } else {
      alert("⚠️ Failed to create invoice. Please try again.");
    }
  } catch (error) {
    console.error("❌ Error creating invoice:", error);
    alert("❌ An error occurred while creating the invoice.");
  }
}


  
</script>


</body>
</html>
