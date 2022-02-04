Hello {{$user->name}}
Thank You for create an account from us, please use this link to verify you email:
{{route('verify',$user->verification_token)}}
