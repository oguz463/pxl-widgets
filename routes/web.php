<?php

use App\Http\Controllers\ParserController;
use App\Models\Person;
use Illuminate\Support\Facades\Route;
use League\Csv\Writer;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::post('/link', [ParserController::class, 'link'])->name('parse.link');
Route::post('/file', [ParserController::class, 'file'])->name('parse.file');

Route::get('export', function () {
    $people = Person::cursor();

    $csv = Writer::createFromFileObject(new SplTempFileObject);
    $csv->insertOne(["name", "address", "checked", "description", "interest", "date_of_birth", "email", "account", "credit_card"]);

    foreach ($people as $person) {
        $csv->insertOne([
            "name" => $person->name,
            "address" => $person->address,
            "checked" => $person->checked,
            "description" => $person->description,
            "interest" => $person->interest,
            "date_of_birth" => $person->date_of_birth,
            "email" => $person->email,
            "account" => $person->account,
            "credit_card" => json_encode($person->credit_card)
        ]);
    }
    
    return response((string) $csv, 200, [
        'Content-Type' => 'text/csv',
        'Content-Transfer-Encoding' => 'binary',
        'Content-Disposition' => 'attachment; filename="exported-' . str_replace(' ', '-', now()) . '.csv"',
    ]);
});

Route::post('/purge', function () {
    \Artisan::call('migrate:fresh');

    return redirect()->route('home');
})->name('purge');
