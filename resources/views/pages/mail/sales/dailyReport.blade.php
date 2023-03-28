
@component('mail::panel')

<p>
	<strong class="text-success">
		{{ $subject }}
	</strong>
</p>

<p>Hi manager<br>
Today, the business has sold {{ number_format($totl_sold) }} items in number 
of amount <strong>UGX. <span class="text-danger">{{ number_format($amount) }}
and the net value is UGX.
@if($netValue > 0)
 <strong class="text-success">{{ number_format($netValue) }}</strong>
 @else
 <strong class="text-danger">{{ number_format($netValue) }}</strong>
@endif

</span></strong>
  </p>


Thanks & Regards,<br>
 @if(isset($companyData))
     {{ $companyData['company_name'] }} E-system
     @else
     {{ env('APP_NAME') }} E-system
     @endif
	 <br>

@endcomponent