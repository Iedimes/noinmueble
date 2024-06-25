<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Applicant;
use App\Models\Subsidio;
use App\Models\City;
use App\Models\Mh;
use App\Models\Visit;
use App\Http\Requests\Admin\Visit\IndexVisit;
use App\Models\MediaDocument;
use App\Models\EducationLevel;
use App\Models\DocumentType;
use App\Models\ApplicantDocument;
use App\Models\ApplicantStatus;
use App\Models\ContactMethod;
use App\Models\ApplicantContactMethod;
use App\Http\Requests\StoreApplicantUser;
use App\Http\Requests\StoreApplicantUserDocument;
use App\Http\Requests\StoreApplicantUserConyuge;
use App\Http\Requests\UpdateApplicantUserConyuge;
use App\Http\Requests\UpdateApplicantUser;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Help\IndexHelp;
use App\Models\Help;
use App\Models\DetailHelp;

use App\Mail\DemoEmail;
use Illuminate\Support\Facades\Mail;


use Brackets\AdminListing\Facades\AdminListing;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->middleware('auth');
    }*/

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        return view('admin.beneficiario.show');

    }

    public function show(Beneficiario $beneficiario)
    {
        //$this->authorize('admin.beneficiario.show', $beneficiario);

        return view('admin.beneficiario.show');

        // TODO your code goes here
    }
}
