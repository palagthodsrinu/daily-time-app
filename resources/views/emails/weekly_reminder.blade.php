<p>Hello {{ $employee->first_name }},</p>

<p>Your total working hours last week were: 
   <strong>{{ number_format($hours, 2) }} hours</strong>.
</p>

<p>Your required weekly hours ({{ $employee->employee_type }}) are:
   <strong>{{ $requiredHours }} hours</strong>.
</p>

<p>Please complete your timesheet properly for the upcoming week.</p>

<p>Thank you,<br>BBIS HR Team</p>
