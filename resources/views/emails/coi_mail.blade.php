<!DOCTYPE html>
<html>
<head>
    <title>COI Application Update</title>
</head>
<body>
    <div style="margin-bottom: 5px;">
        <p class="color:red"><b> * * * * * * *</b></p>
        <b>Note:</b> Do not reply to this auto-generated email. If you encounter any AMIS-related issues, please fill up the support form at <a href="https://forms.gle/TzeTcPn9dZ6WAJ699">https://forms.gle/TzeTcPn9dZ6WAJ699</a>
        <p class="color:red"><b> * * * * * * *</b></p>
    </div>
    <div>
        @if ($mailData->data->status == 'requested')
            <p>Dear Faculty,</p>

            <p>A student named {{ strtoupper($mailData->data->student->name) }} with student number {{ $mailData->data->student->campus_id}}, has applied for COI in your class <b>{{$mailData->data->class->course}} {{$mailData->data->class->section}}</b></p>
            
            <p>The student has requested for the COI with the following remarks/appeal:
                <ul>
                    <li>{{ $mailData->data->student->justification }}</li>
                </ul>
            </p>

            <p>
                To APPROVE this COI application, click on the link below. <br>
                <b>WARNING:</b> Clicking the Link below will automatically approve the request. Alternatively, you may approve COI Applications by going to AMIS (<a href="https://amis.uplb.edu.ph/faculty-coi">amis.uplb.edu.ph</a>), login with your UP Mail, Select Faculty Portal > Consent of Instructor in the left menu. <br>
                Approve COI Link: <a href="{{ env('APP_URL', 'https://amis.uplb.edu.ph') . '/api/approve/external_links/?token=' . $mailData->data->token}}"  target="_blank">Approve COI</a>
                <br><br>
                To DISAPPROVE this COI Application go to AMIS (<a href="https://amis.uplb.edu.ph/faculty-coi">amis.uplb.edu.ph</a>), login with your UP Mail, Select Faculty Portal > Consent of Instructor in the left menu.
            </p>

            <p>You may contact the student thru his/her email: <b>{{$mailData->data->student->email}}</b></p>
            
            <p>Thank you!</p>
        @else
            <p>Dear Student,</p>

            <p>Your COI Application for <b>{{$mailData->data->class->course}} {{$mailData->data->class->section}}</b> has been <b>{{ $mailData->data->status }}</b> by the faculty in charge: <b>{{$mailData->data->class->name}}</b></p>
            
            @if ($mailData->data->status == 'disapproved')
                <p>Your application has been disapproved with the following remark/s:
                    <ul>
                        <li>{{ $mailData->data->reason }}</li>
                    </ul>
                </p>
            @else
                <p>Kindly wait at least 24 hours before your COI approval is reflected in SAIS.</p>  
            @endif

            <p>Thank you!</p>
        @endif
    </div>
</body>
</html>