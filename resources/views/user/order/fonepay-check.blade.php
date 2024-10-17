@extends('user.layout.web')
@section('content')
<form action="{{$paymentDevUrl}}" method="GET" id="payment_form">
    <input type="hidden" name="PID" value="{{$PID}}">
    <input type="hidden" name="MD" value="{{$MD}}">
    <input type="hidden" name="AMT" value="{{$AMT}}">
    <input type="hidden" name="CRN" value="{{$CRN}}">
    <input type="hidden" name="DT" value="{{$DT}}">
    <input type="hidden" name="R1" value="{{$R1}}">
    <input type="hidden" name="R2" value="{{$R2}}">
    <input type="hidden" name="DV" value="{{$DV}}">
    <input type="hidden" name="RU" value="{{$RU}}">
    <input type="hidden" name="PRN" value="{{$PRN}}">

    <input type="submit" value="Click to Pay">
</form>
@endsection