<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Response;
use App\Employee;
use App\City;
use App\State;
use App\Country;
use App\Department;
use App\Division;

class EmployeeManagementController extends Controller
{
    public function __contruc()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $employees = DB::table('employees')
        ->leftJoin('city', 'employees.city_id', '=', 'city.id')
        ->leftJoin('department', 'employees.department_id', '=', 'department.id')
        ->leftJoin('state', 'employees.state_id', '=', 'state.id')
        ->leftJoin('country', 'employees.country_id', '=', 'country.id')
        ->leftJoin('division', 'employees.division_id', '=', 'division.id')
        ->select('employees.*', 'department.name as department_name', 'department.id as department_id', 'division.name as division_name', 'division.id as division_id')
        ->paginate(5);

        return view('employees-mgmt.index', ['employees' => $employees]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::all();
        $states = State::all();
        $countries = Country::all();
        $departments = Department::all();
        $divisions = Division::all();
        
        return view('employees-mgmt.create', [
            'cities'      => $cities,
            'states'      => $states,
            'countries'   => $countries,
            'departments' => $departments,
            'divisions'   => $divisions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $this->validateInput($request);
        // Upload image
        $path = $request->file('picture')->store('avatars');
        $keys = ['lastname', 'firstname', 'middlename', 'address', 'city_id', 'state_id', 'country_id', 'zip',
        'age', 'birthdate', 'date_hired', 'department_id', 'department_id', 'division_id'];
        $input = $this->createQueryInput($keys, $request);
        $input['picture'] = $path;
        // Not implement yet
        $input['company_id'] = 1;
        Employee::create($input);

        

       return redirect('/employee-management');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::find($id);

        if ($employee == null || count($employee) == 0) {
             return redirect()->entended('employee-management');
        }

        $cities = City::all();
        $states = State::all();
        $countries = Country::all();
        $departments = Department::all();
        $divisions = Division::all();
        return view('employees-mgmt/edit', ['employee' => $employee, 'cities' => $cities, 'states' => $states, 'countries' => $countries,
        'departments' => $departments, 'divisions' => $divisions]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $this->validateInput($request);

        $keys = ['lastname', 'firstname', 'middlename', 'address', 'city_id', 'state_id', 'country_id', 'zip',
        'age', 'birthdate', 'date_hired', 'department_id', 'department_id', 'division_id'];
        $input = $this->createQueryInput($keys, $request);

        if($request->file('picture')) {
            $path = $request->file('picture')->store(avatars);
            $input['picture'] = $path;
        }

        Employee::where('id', $id)
        ->update($input);

        return redirect()->intended('employee-management');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Employee::where('id', $id)->delete();

        return redirect()->intended('employee-management');
    }

    private function validateInput($request) {
        $this->validate($request, [
            'lastname' => 'required|max:60',
            'firstname' => 'required|max:60',
            'middlename' => 'required|max:60',
            'address' => 'required|max:120',
            'city_id' => 'required',
            'state_id' => 'required',
            'country_id' => 'required',
            'zip' => 'required|max:10',
            'age' => 'required',
            'birthdate' => 'required',
            'date_hired' => 'required',
            'department_id' => 'required',
            'division_id' => 'required'
        ]);
    }

    public function search(Request $request)
    {
        $contraints = [
            'firstname' =>  $request['firstname'],
            'department.name' => $request['department_name']
        ];

        $employees = $this->doSearchingQuery($contraints);

        $contraints['department_name'] = $request['department_name'];

        return view('employees-mgmt.index',  ['employees' => $employees, 'searchingVals' => $contraints]);
    }

    public function load($name)
    {
         $path = storage_path().'/app/avatars/'.$name;
        if (file_exists($path)) {
            return Response::download($path);
        }
    }

    private function createQueryInput($keys, $request)
    {
        $queryInput = [];
        for($i = 0; $i < sizeof($keys); $i++) {
            $key = $keys[$i];
            $queryInput[$key] = $request[$key];
        }

        return $queryInput;
    }

    private function doSearchingQuery($contraints)
    {
        $query = DB::table('employees')
        ->leftJoin('city', 'employees.city_id', '=', 'city.id')
        ->leftJoin('department', 'employees.department_id', '=', 'department.id')
        ->leftJoin('state', 'employees.state_id', '=', 'state.id')
        ->leftJoin('country', 'employees.country_id', '=', 'country.id')
        ->leftJoin('division', 'employees.division_id', '=', 'division.id')
        ->select('employees.firstname as employee_name', 'employees.*','department.name as department_name', 'department.id as department_id', 'division.name as division_name', 'division.id as division_id');

        $fields = array_keys($contraints);
        $index = 0;

        foreach ($contraints as $contraint)
        {
            if($contraint != null)
            {
                $query = $query->where($fields[$index], 'like', '%'.$contraint.'%');
            }

            $index++;
        }

        return $query->paginate(5);
    }
}
