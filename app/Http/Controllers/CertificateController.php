<?php
namespace App\Http\Controllers;
use App\Models\Certificate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class CertificateController extends Controller
{
    // Simulate course completion for a user
    public function completeCourse(Request $request)
    {
        $userId = $request->input('userID');
        $user = User::findOrFail($userId);

        // For example, set the completion status to 'completed' if the user's progress reaches a certain threshold
        if( $user->completion_status == 'completed'){
            return response()->json(['message' => 'Course already Completed']);
        }elseif (($user->progress == 100) && ($user->completion_status =='not completed')) {
            $user->completion_status = 'completed';
            $user->completed_at = now();
            $user->save();
            return response()->json(['message' => 'Course completion status updated']);
        }else{
            return response()->json(['message' => 'User must reach a progress of 100']);
        }
    }

    // Check if a user has completed a course
    public function checkCourseCompletion(Request $request)
    {
        $userId = $request->input('userID');
        $user = User::findOrFail($userId);

        // checker logic to verify course completion
        if ($user->completion_status === 'completed') {
            //For postman testing
            return response()->json(['message' => 'Course completed']);
        } else {
               //For postman testing
            return response()->json(['message' => 'Course not completed']);
        }
    }

    // Generate a certificate for a user
    public function generateCertificate(Request $request)
    {
        $userId = $request->input('userID');
        $user = User::findOrFail($userId);

        // checker logic to verify course completion
        if ($user->completion_status !== 'completed') {
            return response()->json(['message' => 'Course not completed']);
        }

        // Load the certificate template
        $templatePath = public_path('images/certificate_template.png');
        $certificate = Image::make($templatePath);

        /****
         * Location of the texts are static ie x:160, y:235 so do not alter 
         * 
         */

        $certificate->text($user->name, 160, 235, function ($font) {
            $font->file(public_path('fonts/roboto/Roboto-Black.ttf')); // Font file located at public folder
            $font->size(20); // Adjust the font size as needed
            $font->color('#000000'); // Set the text color
        });
        $certificate->text($user->course, 160, 285, function ($font) {
            $font->file(public_path('fonts/roboto/Roboto-Black.ttf')); 
            $font->size(20);
            $font->color('#000000');
        });
        $certificate->text($user->instructor, 160, 325, function ($font) {
            $font->file(public_path('fonts/roboto/Roboto-Black.ttf'));
            $font->size(20);
            $font->color('#000000');
        });
              
        $certificateIdentifier = time();

        $certificatePath = $user->name . $user->id . '_' . $certificateIdentifier . '.png';

        Storage::disk('public')->put($certificatePath, $certificate->stream()->__toString());

        $storagePath = storage_path('app/public/' . $certificatePath);

        $user->addMedia($storagePath)->toMediaCollection('certificates');

        Certificate::create([
            'name' => $user->name,
            'userid' => $user->id,
            'certificate_path' => $certificatePath
        ]);



        return response()->json(['message' => 'Certificate generated successfully']);
    }

    // Send the generated certificate to a user's email
    public function sendCertificateByEmail(Request $request)
    {
        $userId = $request->input('userID');
        $user = User::findOrFail($userId);
    
        // checker logic to verify course completion
        if ($user->completion_status !== 'completed') {
            return response()->json(['message' => 'Course not completed']);
        }
    
        // Retrieve the generated certificate from the media library
        $certificate = $user->getFirstMedia('certificates');
    
        if ($certificate) { //The blade view file is in the resources folder
            // Email subject
            $title = "Congratulations on Completing $user->course";
    
            // Pass data to the email view
            $data = [
                'title' => $title,
                'user' => $user,
            ];
    
            // Send the email
            Mail::send('emails.certificate', $data, function ($message) use ($user, $title, $certificate) {
                $message
                    ->to($user->email)
                    ->subject($title);
    
                // Attaching the certificate to the email
                $filePath = $certificate->getPath();
                $fileName = 'certificate.png';
                
                $message->attach($filePath, [
                    'as' => $fileName,
                    'mime' => 'image/png', // Set the mime type for PNG
                ]);
            });
    
            return response()->json(['message' => 'Certificate sent to email successfully']);
        } else {
            return response()->json(['message' => 'Certificate not found']);
        }
    }
    

    // function for Certificate Deletion
    public function deleteExpiredCertificates()
    {
     
    //Will be a protected route only admin can call
    {
        //3months validity
        $expirationDate = Carbon::now()->subMonths(3);

        //jumps 3months ahead for testing purposes comment the line above to use the one below
        //$expirationDate = Carbon::now()->addMonths(3); uncomment this for testing purposes

        // Find expired certificates in the 'certificates' media collection
        $expiredCertificatesPath = Certificate::where('created_at','<',$expirationDate)->get();
        $expiredCertificates = Media::where('collection_name', 'certificates')
            ->where('created_at', '<', $expirationDate)
            ->get();

        // Delete each expired certificate
        foreach ($expiredCertificates as $certificate) {
            $certificate->delete();
        }
        foreach($expiredCertificatesPath as $path){
            $path->delete();
        }

        return response()->json(['message' => 'Certificates Deleted']);
    }
}
    
    
}


    


