<p>
    Please verify your email by clicking the link below:
</p>
<p>
    <a href="{{ url('/verify-email?token=' . $user->email_verification_token) }}">Verify Email</a>
</p>