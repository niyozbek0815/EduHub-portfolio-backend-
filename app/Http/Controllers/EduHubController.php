<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\User_link;
use App\Models\Cources;
use App\Models\Student_buy_course;
use App\Models\Courses_sharxlar;
use App\Models\Instructor_about;
use Illuminate\Support\Facades\DB;
use App\Models\Instructor_sharxlar;
use Illuminate\Support\Facades\Hash;
use App\Models\Instructor_education;
use App\Models\Instructor_tajriba;
use App\Models\Cources_includes;
use App\Models\Category;
use App\Models\Video_tip;
use App\Models\Cources_discription;
use Auth;

class EduHubController extends Controller
{
/**
 * student profil
 */


    public function student_single($id, $id2)
    {
       /* $edit=0;
        if(($id==$id2)){
            $edit=1;
        }
        $sharx = Instructor_sharxlar::where('user_id', '=', $id)->orderBy('created_at', 'desc')->paginate(16);
        $avg = Instructor_sharxlar::where('user_id', '=', $id)->avg('reyting');
        $sharx_count = Instructor_sharxlar::where('user_id', '=', $id)->count();
        if (!$sharx_count == 0) {
            $stars5 = (Instructor_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 5)
                ->count()) * 100 / $sharx_count;
            $stars3 = (Instructor_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 3)
                ->count()) * 100 / $sharx_count;
            $stars4 = (Instructor_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 4)
                ->count()) * 100 / $sharx_count;
            $stars2 = (Instructor_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 2)
                ->count()) * 100 / $sharx_count;
            $stars1 = (Instructor_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 1)
                ->count()) * 100 / $sharx_count;
        } else {
            $stars5 = 0;
            $stars1 = 0;
            $stars2 = 0;
            $stars3 = 0;
            $stars4 = 0;
        }

        $tajriba = Instructor_tajriba::where('user_id', '=', $id)->orderBy('date1')->get();
        $edu = Instructor_education::where('user_id', '=', $id)->orderBy('date1')->get();



        $user = User::findOrFail($id);
        $url = User_link::find($id);
        $courses = Cources::select('*')->where('ins_id', '=', $id)->withCount('students')->withCount('sharxlar')->withAvg('sharxlar', 'reyting')->paginate();
        $count_cources = User::find($id)->cources->count();
        $about = instructor_about::where("user_id", '=', $id)->first();
        $student_count = 0;

        foreach ($courses as $c) {

            $a = DB::select('select * from student_buy_courses where cours_id = ?', [$c->id]);
            foreach ($a as $d) {
                $student_count = $student_count + 1;
            }
        }*/
        return view("student.student_profil", [ ]);
    }

    /**
     * course_single
     */


    public function cource_sharx(Request $request, $id,)
    {
        $data = $request->validate([
            'reviewRating' => 'in:1,,2,3,4,5|required',
            'text' => 'required',
            'cource_id' => 'required'
        ]);

        $n = new Courses_sharxlar();
        $n->reyting = $data['reviewRating'];
        $n->user_id = $id;
        $n->cours_id = $data['cource_id'];
        $n->sharx = $data['text'];
        $n->save();
        return redirect('/course-single/' . $data['cource_id']);
    }

    public function course_single($id, $id2)
    {
        $edit = 0;
        if (!$id == $id2) {
            $edit = 1;
        }
        $course = Cources::select('*')->withCount('students')
            ->withCount('video')->withCount('sharxlar')->withAvg('sharxlar', 'reyting')->find($id);
        $courses = Cources::select('*')->where('ins_id', '=', $course->user->id)->paginate();
        $students = 0;
        $user = User::withCount('ins_sharx')->withCount('cources')->withAvg('ins_sharx', 'reyting')->find($course->ins_id);

        $include = Cources_includes::where('cours_id', '=', $id)->get();
        $desc = Cources_discription::get();

        $sharx = Courses_sharxlar::where('user_id', '=', $course->user->id)->orderBy('created_at', 'desc')->paginate(16);
        if (!$course->sharxlar_count == 0) {
            $stars5 = (Courses_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 5)
                ->count()) * 100 / $course->sharxlar_count;
            $stars3 = (Courses_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 3)
                ->count()) * 100 / $course->sharxlar_count;
            $stars4 = (Courses_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 4)
                ->count()) * 100 / $course->sharxlar_count;
            $stars2 = (Courses_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 2)
                ->count()) * 100 / $course->sharxlar_count;
            $stars1 = (Courses_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 1)
                ->count()) * 100 / $course->sharxlar_count;
        } else {
            $stars5 = 0;
            $stars1 = 0;
            $stars2 = 0;
            $stars3 = 0;
            $stars4 = 0;
        }

        foreach ($courses as $c) {

            $a = DB::select('select * from student_buy_courses where cours_id = ?', [$c->id]);
            foreach ($a as $d) {
                $students = $students + 1;
            }
        }



        $auth_id = auth()->user()->id;
        $buy = Student_buy_course::where('user_id', '=', $auth_id)->where('cours_id', '=', $id)->first();
        $video = Video_tip::where('cource_id', '=', $id)->get();
        return view("course.course_single", [
            'cource' => $course, 'desc' => $desc, 'user' => $user, 'students' => $students,
            'stars5' => $stars5, 'stars4' => $stars4, 'stars3' => $stars3, 'stars2' => $stars2, 'stars1' => $stars1, 'sharx' => $sharx,
            'includes' => $include, 'video' => $video, 'buy' => $buy,'edit'=>$edit
        ]);
    }



    /*
teacher profil

 */


    public function teacher_sharx(Request $request, $id,)
    {
        $data = $request->validate([
            'reviewRating' => 'in:1,,2,3,4,5|required',
            'text' => 'required',
            'user_id' => 'required'
        ]);

        $n = new Instructor_sharxlar();
        $n->reyting = $data['reviewRating'];
        $n->user_id = $id;
        $n->student_id = $data['user_id'];
        $n->sharx = $data['text'];
        $n->save();
        return redirect('/instructor-single/' . $id.'/'.$id);
    }

    public function instructor_single($id, $id2)
    {
        $edit=0;
        if(($id==$id2)){
            $edit=1;
        }
        $sharx = Instructor_sharxlar::where('user_id', '=', $id)->orderBy('created_at', 'desc')->paginate(16);
        $avg = Instructor_sharxlar::where('user_id', '=', $id)->avg('reyting');
        $sharx_count = Instructor_sharxlar::where('user_id', '=', $id)->count();
        if (!$sharx_count == 0) {
            $stars5 = (Instructor_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 5)
                ->count()) * 100 / $sharx_count;
            $stars3 = (Instructor_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 3)
                ->count()) * 100 / $sharx_count;
            $stars4 = (Instructor_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 4)
                ->count()) * 100 / $sharx_count;
            $stars2 = (Instructor_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 2)
                ->count()) * 100 / $sharx_count;
            $stars1 = (Instructor_sharxlar::where('user_id', '=', $id)
                ->where('reyting', '=', 1)
                ->count()) * 100 / $sharx_count;
        } else {
            $stars5 = 0;
            $stars1 = 0;
            $stars2 = 0;
            $stars3 = 0;
            $stars4 = 0;
        }

        $tajriba = Instructor_tajriba::where('user_id', '=', $id)->orderBy('date1')->get();
        $edu = Instructor_education::where('user_id', '=', $id)->orderBy('date1')->get();



        $user = User::findOrFail($id);
        $url = User_link::find($id);
        $courses = Cources::select('*')->where('ins_id', '=', $id)->withCount('students')->withCount('sharxlar')->withAvg('sharxlar', 'reyting')->paginate();
        $count_cources = User::find($id)->cources->count();
        $about = instructor_about::where("user_id", '=', $id)->first();
        $student_count = 0;

        foreach ($courses as $c) {

            $a = DB::select('select * from student_buy_courses where cours_id = ?', [$c->id]);
            foreach ($a as $d) {
                $student_count = $student_count + 1;
            }
        }
        return view("inctructor.instructor-single", [
            'sharx_count' => $sharx_count,
            'about' => $about, 'stars' => $avg, 'user' => $user, 'url' => $url,
            'student' => $student_count, 'c_count' => $count_cources, 'cources' => $courses,
            'stars5' => $stars5, 'stars4' => $stars4, 'stars3' => $stars3, 'stars2' => $stars2, 'stars1' => $stars1,
            'sharx' => $sharx, 'tajriba' => $tajriba, 'edu' => $edu, 'edit'=>$edit
        ]);
    }

    /*
User Profil teacher va student uchun
*/



    public function user_profil($id)
    {
        $user = User::findOrFail($id);
        $url = User_link::find($id);

        return view('sign.profil', ['nu' => $user, "url" => $url]);
    }
    public function user_update(Request $req, $id)
    {
        $n = User::find($id);
        $user = User::find($id);
        $user_link = User_link::find($id);
        if (isset($user_link)) {
            $url = $user_link;
        } else {
            $url = new User_link();
        }

        $data = $req->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);


        if ($user->email === $req->email) {
            $data = $req->validate([
                'email' => ['required', 'string', 'email', 'max:255'],
            ]);
            $n->email = $req->email;
        } else {
            $data = $req->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);
            $n->email = $req->email;
        }
        if (strlen($req->sname) >= 1) {
            $data = $req->validate([
                'sname' => ['required', 'string', 'max:255'],
            ]);
            $n->sname = $req->sname;
        }
        if (strlen($req->tell) >= 1) {
            $data = $req->validate([
                'tell' => 'required|min:9|numeric'
            ]);
            $n->tell = $req->tell;
        }
        if (strlen($req->instagram) >= 1) {
            $data = $req->validate([
                'instagram' => ['required', 'string', 'max:255'],
            ]);
            $url->instagram = $req->instagram;
            $url->user_id = $id;
        }
        if (strlen($req->telegram) >= 1) {
            $data = $req->validate([
                'telegram' => ['required', 'string', 'max:255'],
            ]);
            $url->telegram = $req->telegram;
            $url->user_id = $id;
        }
        if (strlen($req->facebook) >= 1) {
            $data = $req->validate([
                'facebook' => ['required', 'string', 'max:255'],
            ]);
            $url->facebook = $req->facebook;
            $url->user_id = $id;
        }
        if (strlen($req->youtube) >= 1) {
            $data = $req->validate([
                'youtube' => ['required', 'string', 'max:255'],
            ]);
            $url->youtube = $req->youtube;
            $url->user_id = $id;
        }


        if ($req->has("img")) {
            $data = $req->validate([
                'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5048',
            ]);
            $filemodel = $req->file('img');
            $fileNameWithExt = $filemodel->getClientOriginalName();
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $ext = $filemodel->getClientOriginalExtension();
            $fileNameToStory = $filename . '_' . time() . "." . $ext;
            $path = $filemodel->storeAs('public/user/', $fileNameToStory);
            $n->img = $fileNameToStory;
        } else {
            $fileNameToStory = "user.png";
            $n->img = $fileNameToStory;
        }

        if (strlen($req->oldpassword) >= 1  and (!Hash::check($req->oldpassword, $user->password))) {

            $data = $req->validate([
                'oldpassword' => ['required', 'string', 'min:8'],
            ]);
            $pass = "Eski parolni hato kiritdingiz";
            return redirect()->back()->withErrors($pass)->withInput();
        } else {
            if (strlen($req->newpassword) >= 1 and (!strlen($req->resetpassword) >= 1)) {
                $pass = "Reset Password bo'limini toldiring";
                $data = $req->validate([
                    'oldpassword' => ['required', 'string', 'min:8',],
                    'newpassword' => ['required', 'string', 'min:8',],
                    'resetpassword' => ['required', 'string', 'min:8',],
                ]);
                return redirect()->back()->withErrors($pass)->withInput();
            }
            if (strlen($req->resetpassword) >= 1 and (!strlen($req->newpassword) >= 1)) {
                $pass = "New Password bo'limini toldiring";
                $data = $req->validate([
                    'resetpassword' => ['required', 'string', 'min:8'],
                ]);
                return redirect()->back()->withErrors($pass)->withInput();
            }
            if ($req->newpassword === $req->oldpassword and strlen($req->newpassword) >= 1) {
                $pass = "Yangi parolni qayta kiriting. Eski parolingizni takrorladingiz.";
                return redirect()->back()->withErrors($pass)->withInput();
            }
            if ((strlen($req->resetpassword) >= 1  and strlen($req->newpassword) >= 1) and (!$req->newpassword === $req->resetpassword)) {
                $pass = "Yangi parollar bir xil emas";
                $data = $req->validate([
                    'resetpassword' => ['required', 'string', 'min:8', 'confirmed'],
                    'newpassword' => ['required', 'string', 'min:8', 'confirmed'],
                ]);
                return redirect()->back()->withErrors($pass)->withInput();
            }
            if ((strlen($req->newpassword) >= 1  and strlen($req->resetpassword) >= 1) and $req->newpassword === $req->resetpassword) {
                $data = $req->validate([
                    'resetpassword' => ['required', 'string', 'min:8',],
                    'newpassword' => ['required', 'string', 'min:8'],
                ]);
                $password = Hash::make($data['newpassword']);
                $n->password = $password;
            }
        }

        $n->name = $req->name;
        if (strlen($req->youtube) >= 1 or  (strlen($req->telegram) >= 1) or  (strlen($req->instagram) >= 1) or (strlen($req->facebook) >= 1)) {
            $url->save();
        }

        $n->save();
        return redirect()->back();
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("index");
    }
    public function about()
    {
        return view("pages.about");
    }
    public function courses()
    {
        return view("course.course");
    }

    public function course_search()
    {
        return view("course.course_search");
    }
    public function course_category()
    {
        return view("course.course_category");
    }
    public function instructor()
    {
        return view("inctructor.instructor");
    }

    public function teams()
    {
        return view("pages.teams");
    }
    public function police()
    {
        return view("pages.police");
    }
    public function blog()
    {
        return view("blog.blog");
    }
    public function contact()
    {
        return view("pages.contact");
    }
    public function card()
    {
        return view("korzinka.card");
    }
    public function card_checkout()
    {
        return view("korzinka.card_checkout");
    }
    public function signin()
    {
        return view("sign.sign-in");
    }
    public function forgotPassword()
    {
        return view("sign.forget-password");
    }
    public function signup()
    {
        return view("sign.sign-up");
    }
}
