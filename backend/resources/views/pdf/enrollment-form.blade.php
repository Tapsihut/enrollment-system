<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Enrollment Form</title>

<style>
@page{margin:10mm}
*{box-sizing:border-box}
body{margin:0;padding:0;font-family:DejaVu Sans,sans-serif;font-size:10px;color:#000;line-height:1.1}
.copy{width:100%;page-break-inside:avoid;margin-bottom:6px}
.copy-title{font-size:10px;font-weight:bold;margin-bottom:3px}
.header{text-align:center;margin-bottom:4px}
.header h2{margin:0;font-size:16px}
.header p{margin:1px 0;font-size:9px}
.form-title{text-align:center;font-size:14px;font-weight:bold;margin:5px 0 6px}
.info{width:100%;border-collapse:collapse;margin-bottom:4px}
.info td{padding:2px 1px;vertical-align:bottom}
.label{font-weight:bold;white-space:nowrap}
.line{display:block;border-bottom:1px solid #000;min-height:12px;padding-left:2px}
.subject-table{width:100%;border-collapse:collapse;table-layout:fixed;margin-top:3px}
.subject-table th,.subject-table td{border:1px solid #000;padding:2px;font-size:8px}
.subject-table th{text-align:center;font-weight:bold}
.subject-table td{text-align:center}
.subject-table td:nth-child(3){text-align:left}
.subject-table th:nth-child(1),.subject-table td:nth-child(1){width:12%}
.subject-table th:nth-child(2),.subject-table td:nth-child(2){width:13%}
.subject-table th:nth-child(3),.subject-table td:nth-child(3){width:32%}
.subject-table th:nth-child(4),.subject-table td:nth-child(4){width:8%}
.subject-table th:nth-child(5),.subject-table td:nth-child(5){width:10%}
.subject-table th:nth-child(6),.subject-table td:nth-child(6){width:15%}
.subject-table th:nth-child(7),.subject-table td:nth-child(7){width:10%}
.footer{margin-top:4px}
.or{text-align:right;font-size:9px;font-weight:bold;margin:4px 0}
.signature{width:100%;border-collapse:collapse;margin-top:5px}
.signature td{width:50%;text-align:center;vertical-align:top}
.signature b{font-size:10px}
.signature-line{display:inline-block;width:180px;border-bottom:1px solid #000;margin-top:15px}
.generated{text-align:center;margin-top:5px;font-size:8px}
.cut{border-top:1px dashed #000;margin:6px 0;padding-top:2px;text-align:center;font-size:8px;font-weight:bold}
table{page-break-inside:auto}
tr{page-break-inside:avoid}
thead{display:table-header-group}
tfoot{display:table-footer-group}
</style>
</head>

<body>

@php
    $courseName = strtoupper($enrollment->course->name ?? '');

    if (
        str_contains($courseName, 'BSIT') ||
        str_contains($courseName, 'INFORMATION TECHNOLOGY')
    ) {
        $programHead = 'Robert Jhun V. Lagang, MSIT';
    } elseif (
        str_contains($courseName, 'BSBA') ||
        str_contains($courseName, 'BUSINESS ADMINISTRATION')
    ) {
        $programHead = 'Jerome M. Seraspe, MBA';
    } elseif (
        str_contains($courseName, 'CRIMINOLOGY')
    ) {
        $programHead = 'Jayson Gerona, PhD.';
    } elseif (
        str_contains($courseName, 'ACCOUNTING') ||
        str_contains($courseName, 'ACCOUNTANCY') ||
        str_contains($courseName, 'BSA')
    ) {
        $programHead = 'Feriza B. Aban, CPA, MBA';
    } else {
        $programHead = 'College Dean / Program Head';
    }
@endphp

@for($copy=1;$copy<=2;$copy++)

<div class="copy">

<div class="copy-title">
{{ $copy==1 ? "Registrar's Copy" : "Student's Copy" }}
</div>

<div class="header">
<h2>ST. FRANCIS XAVIER COLLEGE</h2>
<p>Barangay 5, San Francisco, Agusan del Sur</p>
</div>

<div class="form-title">
ENROLLMENT FORM
</div>

<table class="info">

<tr>
<td class="label" width="10%">ID No.:</td>
<td width="20%">
<span class="line">{{ $student->student_number }}</span>
</td>

<td class="label" width="12%">Semester:</td>
<td width="18%">
<span class="line">{{ $enrollment->semester->name ?? 'N/A' }}</span>
</td>

<td class="label" width="10%">Program:</td>
<td>
<span class="line">{{ $enrollment->course->name ?? 'N/A' }}</span>
</td>
</tr>

<tr>
<td class="label">Name:</td>
<td>
<span class="line">
{{ strtoupper($student->last_name.', '.$student->first_name.' '.$student->middle_name) }}
</span>
</td>

<td class="label">Year</td>
<td>
<span class="line">{{ $enrollment->year_level }}</span>
</td>

<td class="label">S.Y.:</td>
<td>
<span class="line">
{{ $enrollment->schoolYear->school_year ?? 'N/A' }}
</span>
</td>
</tr>

<tr>
<td class="label">Student Type</td>
<td>
<span class="line">
{{ $student->student_type ?? 'Regular' }}
</span>
</td>
<td colspan="4"></td>
</tr>

</table>

<table class="subject-table">

<thead>
<tr>
<th>Sched Code</th>
<th>Course Code</th>
<th>Description</th>
<th>Units</th>
<th>Section</th>
<th>Time</th>
<th>Day</th>
</tr>
</thead>

<tbody>

@foreach($enrollment->subjects as $subject)

<tr>
<td>{{ $subject->schedule_code ?? '' }}</td>
<td>{{ $subject->code }}</td>
<td>{{ $subject->title }}</td>
<td>{{ $subject->units }}</td>
<td>{{ $subject->section ?? '' }}</td>
<td>{{ $subject->time ?? '' }}</td>
<td>{{ $subject->day ?? '' }}</td>
</tr>

@endforeach

</tbody>
</table>

<div class="or">
OR No. ___________________________
</div>

<table class="signature">

<tr>

<td>
<b>Prepared by:</b>
<br>

<span class="signature-line">
{{ strtoupper($student->first_name.' '.$student->middle_name.' '.$student->last_name) }}
</span>

<br>
Student Signature
</td>

<td>
<b>Approved by:</b>
<br>

<span class="signature-line">
{{ strtoupper($programHead) }}
</span>

<br>
College Dean / Program Head
</td>

</tr>

<tr>
<td colspan="2" class="generated">
Date Generated:
{{ now()->format('F d, Y h:i A') }}
</td>
</tr>

</table>

</div>

@if($copy==1)
<div class="cut">
✂ CUT HERE
</div>
@endif

@endfor

</body>
</html>