<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<title>Enrollment Form</title>

<style>

@page {
    margin: 12mm;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 0;
    font-family: DejaVu Sans, sans-serif;
    font-size: 10px;
    color: #000;
    line-height: 1.2;
}

/* ===============================
   COPY
=================================*/

.copy {
    width: 100%;
    page-break-inside: avoid;
    margin-bottom: 10px;
}

.copy-title {
    font-size: 11px;
    font-weight: bold;
    margin-bottom: 6px;
}

/* ===============================
   HEADER
=================================*/

.header {
    text-align: center;
    margin-bottom: 8px;
}

.header h2 {
    margin: 0;
    font-size: 18px;
    font-weight: bold;
}

.header p {
    margin: 2px 0;
    font-size: 10px;
}

.form-title {
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    margin: 10px 0 12px;
}

/* ===============================
   STUDENT INFO
=================================*/

.info {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
}

.info td {
    padding: 3px 2px;
    vertical-align: bottom;
}

.label {
    font-weight: bold;
    white-space: nowrap;
}

.line {
    display: block;
    border-bottom: 1px solid #000;
    min-height: 14px;
    padding-left: 3px;
}

/* ===============================
   SUBJECT TABLE
=================================*/

.subject-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    margin-top: 5px;
}

.subject-table th,
.subject-table td {
    border: 1px solid #000;
    padding: 3px;
    font-size: 9px;
}

.subject-table th {
    text-align: center;
    font-weight: bold;
}

.subject-table td {
    text-align: center;
}

.subject-table td:nth-child(3) {
    text-align: left;
}

/* Column Widths */

.subject-table th:nth-child(1),
.subject-table td:nth-child(1) {
    width: 12%;
}

.subject-table th:nth-child(2),
.subject-table td:nth-child(2) {
    width: 13%;
}

.subject-table th:nth-child(3),
.subject-table td:nth-child(3) {
    width: 32%;
}

.subject-table th:nth-child(4),
.subject-table td:nth-child(4) {
    width: 8%;
}

.subject-table th:nth-child(5),
.subject-table td:nth-child(5) {
    width: 10%;
}

.subject-table th:nth-child(6),
.subject-table td:nth-child(6) {
    width: 15%;
}

.subject-table th:nth-child(7),
.subject-table td:nth-child(7) {
    width: 10%;
}

/* ===============================
   FOOTER
=================================*/

.footer {
    margin-top: 8px;
}

.or {
    text-align: right;
    font-size: 11px;
    font-weight: bold;
    margin: 8px 0;
}

/* ===============================
   SIGNATURE
=================================*/

.signature {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.signature td {
    width: 50%;
    text-align: center;
    vertical-align: top;
}

.signature b {
    font-size: 11px;
}

.signature-line {
    display: inline-block;
    width: 180px;
    border-bottom: 1px solid #000;
    margin-top: 24px;
}

.generated {
    text-align: center;
    margin-top: 10px;
    font-size: 9px;
}

/* ===============================
   CUT LINE
=================================*/

.cut {
    border-top: 2px dashed #000;
    margin: 12px 0;
    padding-top: 4px;
    text-align: center;
    font-size: 9px;
    font-weight: bold;
}

/* ===============================
   PRINT
=================================*/

table {
    page-break-inside: auto;
}

tr {
    page-break-inside: avoid;
}

thead {
    display: table-header-group;
}

tfoot {
    display: table-footer-group;
}

</style>

</head>

<body>

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

<td class="label" width="10%">
ID No.:
</td>

<td width="20%">
<span class="line">
{{ $student->student_number }}
</span>
</td>

<td class="label" width="12%">
Semester:
</td>

<td width="18%">
    <span class="line">
        {{ $enrollment->semester->name ?? 'N/A' }}
    </span>
</td>

<td class="label" width="10%">
Program:
</td>

<td>
<span class="line">
{{ $enrollment->course->name }}
</span>
</td>

</tr>

<tr>

<td class="label">
Name:
</td>

<td>

<span class="line">

{{ strtoupper($student->last_name.', '.$student->first_name.' '.$student->middle_name) }}

</span>

</td>
<td class="label">
Year
</td>

<td>

<span class="line">

{{ $enrollment->year_level }}

</span>

</td>
<td class="label">
S.Y.:
</td>

<td>

<span class="line">

{{ $enrollment->schoolYear->school_year ?? 'N/A' }}

</span>

</td>

</tr>

<tr>

<td class="label">
Student Type
</td>

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

<th width="12%">Sched Code</th>

<th width="13%">Course Code</th>

<th>Description</th>

<th width="8%">Units</th>

<th width="10%">Section</th>

<th width="15%">Time</th>

<th width="10%">Day</th>

</tr>

</thead>

<tbody>

@foreach($enrollment->subjects as $subject)

<tr>

<td>

{{ $subject->schedule_code ?? '' }}

</td>

<td>

{{ $subject->code }}

</td>

<td>

{{ $subject->title }}

</td>

<td align="center">

{{ $subject->units }}

</td>

<td>

{{ $subject->section ?? '' }}

</td>

<td>

{{ $subject->time ?? '' }}

</td>

<td>

{{ $subject->day ?? '' }}

</td>

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

<br><br>

<span class="signature-line">
    {{ strtoupper($student->first_name.' '.$student->middle_name.' '.$student->last_name) }}
</span>

<br>

Student Signature

</td>

<td>

<b>Approved by:</b>

<br><br>

<span class="signature-line">
    ROBERT JHUN V. LAGANG, MSIT
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


@endif

@endfor

</body>

</html>