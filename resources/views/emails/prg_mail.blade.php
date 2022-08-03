<!DOCTYPE html>
<html>
<head>
    <title>Prerog Application Update</title>
</head>
<body>
    <div style="margin-bottom: 5px;">
        <p class="color:red"><b> * * * * * * *</b></p>
        <b>Note:</b> Do not reply to this auto-generated email. If you encounter any AMIS-related issues, please fill up the support form at <a href="https://forms.gle/TzeTcPn9dZ6WAJ699">https://forms.gle/TzeTcPn9dZ6WAJ699</a>
        <p class="color:red"><b> * * * * * * *</b></p>
    </div>
    <div>
        @if ($mailData->data->status == 'REQUESTED')
            <p>Dear Faculty,</p>

            <p>A student named {{ strtoupper($mailData->data->student->name) }} with student number {{ $mailData->data->student->campus_id}}, has applied for a Prerog in your class <b>{{$mailData->data->class->course}} {{$mailData->data->class->section}}</b></p>
            
            <p>The student has applied for the Prerog with the following remarks/appeal:
                <ul>
                    <li>{{ $mailData->data->student->justification }}</li>
                </ul>
            </p>

            <p>
                To ACCEPT this Prerog application, click on the link below. <br>
                <b>WARNING:</b> Clicking the Link below will automatically accept the request. Alternatively, you may accept Prerog Applications by going to AMIS (<a href="https://amis.uplb.edu.ph/faculty/prerogative-enrollment">amis.uplb.edu.ph</a>), login with your UP Mail, Select Faculty Portal > Prerogative Enrollment in the left menu. <br>
                Accept Prerog Link: <a href="{{ env('APP_URL', 'https://amis.uplb.edu.ph') . '/api/accept/external_links/?token=' . $mailData->data->token}}"  target="_blank">ACCEPT PREROG</a>
                <br><br>
                To DISAPPROVE this Prerog Application go to AMIS (<a href="https://amis.uplb.edu.ph/faculty/prerogative-enrollment">amis.uplb.edu.ph</a>), login with your UP Mail, Select Faculty Portal > Prerogative Enrollment in the left menu.
            </p>

            <p>You may contact the student thru his/her email: <b>{{$mailData->data->student->email}}</b></p>
            
            <p>Thank you!</p>
        @elseif ($mailData->data->status == 'ACCEPTED')
            <p>Dear OCS Admin,</p>

            <p>A student named {{ strtoupper($mailData->data->student->name) }} with student number {{ $mailData->data->student->campus_id}}, has applied for a Prerog in <b>{{$mailData->data->class->course}} {{$mailData->data->class->section}}</b> and has been approved by the Faculty-in-Charge: {{ $mailData->data->class->name }}</p>
            
            <p>The student has applied for the Prerog with the following remarks/appeal:
                <ul>
                    <li>{{ $mailData->data->student->justification }}</li>
                </ul>
            </p>

            <p>
                You may approve/disapprove Prerog Applications by going to AMIS (<a href="https://amis.uplb.edu.ph/admin/prerogative-enrollment">amis.uplb.edu.ph</a>), login with your UP Mail, Select Admin Portal > Prerogative Enrollment in the left menu.
            </p>

            {{-- <p>
                To APPROVE this Prerog application, click on the link below. <br>
                <b>WARNING:</b> Clicking the Link below will automatically approve the request. Alternatively, you may approve Prerog Applications by going to AMIS (<a href="https://amis.uplb.edu.ph/admin/prerogative-enrollment">amis.uplb.edu.ph</a>), login with your UP Mail, Select Faculty Portal > Prerogative Enrollment in the left menu. <br>
                Approve Prerog Link: <a href="{{ env('APP_URL', 'https://amis.uplb.edu.ph') . '/api/external_link/approve/?token=' . $mailData->data->token}}"  target="_blank">APPROVE PREROG</a>
                <br><br>
                To DISAPPROVE this Prerog Application go to AMIS (<a href="https://amis.uplb.edu.ph/admin/prerogative-enrollment">amis.uplb.edu.ph</a>), login with your UP Mail, Select Admin Portal > Prerogative Enrollment in the left menu.
            </p> --}}

            <p>You may contact the student thru his/her email: <b>{{$mailData->data->student->email}}</b></p>
            
        {{-- Try to put this code into one if else condition --}}
        @elseif ($mailData->data->status == 'APPROVED')
            <p>Dear Student,</p>
            
            <p>Your Prerog/Change of Matriculation Application for <b>{{$mailData->data->class->course}} {{$mailData->data->class->section}}</b> has been approved by the faculty in charge: {{ $mailData->data->class->name }} and the Office of the College Secretary administering your program.</p>

            <p>Kindly wait at least 24 hours before your Prerog approval is reflected in SAIS.</p>  

            <p>Thank you!</p>
        @else
            <p>Dear Student,</p>
                
            <p>Your Prerog/Change of Matriculation Application for <b>{{$mailData->data->class->course}} {{$mailData->data->class->section}}</b> has been disapproved by the 
                @if($mailData->data->role == 'faculties')
                    faculty-in-charge: {{ $mailData->data->class->name }} with note/comment:
                @else
                    <b>Office of the College Secretary</b> administering your program with note/comment:
                @endif
            </p>

            <p>
                <ul>
                    <li>{{ $mailData->data->reason }}</li>
                </ul>
            </p>

            <p>Thank you!</p>
        @endif
    </div>
</body>
</html>