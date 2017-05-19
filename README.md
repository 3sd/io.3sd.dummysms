# io.3sd.dummysms

This CiviCRM extension provides a dummy SMS provider. 
It allows you to send/receive fake SMS messages.

## Configuration
Administer->System Settings->SMS Providers

Create a new SMS provider with type "DummySms".
Parameters (username etc.) do not matter and can be 
set to anything.

## Usage
To receive an SMS message use the SmsProvider.receive function:

```cv api SmsProvider.receive sequential=1 from_number="01234" content="hi" ```

To send an SMS use the built-in CiviCRM functions (eg. Contact->Outbound SMS activity).
* Sent SMS messages will appear in the ConfigAndLog/sms_out.log file.

## Notes
Message ID = timestamp