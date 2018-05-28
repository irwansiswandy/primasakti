<p>
  <b>Sender's Info :</b><br />
  Name : {{ $sender_name }}<br />
  @if ($sender_company != null)
    Company : {{ $sender_company }}<br />
    E-mail : {{ $sender_email }}<br />
    Contact Number : {{ $sender_contact }}
  @else
    E-mail : {{ $sender_email }}<br />
    Contact Number : {{ $sender_contact }}
  @endif
</p>

<p>
  <b>Message :</b><br />
  {!! nl2br(e($sender_message)) !!}
</p>
