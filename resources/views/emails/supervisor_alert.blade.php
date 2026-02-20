<p>Hello Supervisor,</p>

<p>The employee <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong> 
has logged only 
<strong>{{ number_format($hours, 2) }} hours</strong> last week.</p>

<p>The required weekly hours for the employee type ({{ $employee->employee_type }}) is 
<strong>{{ $requiredHours }} hours</strong>.</p>

<p>Please follow up with the employee regarding missing time entries.</p>

<p>Thank you,<br>BBIS HR Team</p>
