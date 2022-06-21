<?php

namespace App\Http\Controllers;

use App\Jobs\RecordsCollection;
use App\Parser\ParseJson;
use App\Parser\ParseCsv;
use App\Parser\Parser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;

class ParserController extends Controller
{

    /**
     * Reading the content from URL (Content will be read as a JSON string, will not be decoded)
     *
     * Parse it through via @property Parser $parser (Read content will be parsed into a LazyCollection for the performance)
     * Matching records will be thrown to the queue for execution (Avoiding any halt while processing.)
     *
     * @param Request $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function link(Request $request)
    {
        $validated = $request->validate([
            'link' => 'required|url'
        ]);
        
        $source = Http::get($validated["link"]);

        if ($source->getHeader('Content-Type')[0] === "application/json") {
            $parsedJson = Parser::parse($source->body(), new ParseJson);
    
            if (!$parsedJson) {
                return back()->withErrors(['link' => 'An error occured.']);
            }
    
            $filteredParsedJson = $this->filterCreditCardNumberHasConsecutiveNumber($this->filterAge($parsedJson));

            RecordsCollection::dispatch($filteredParsedJson->all())->onQueue('medium');

            return back()->with(['message' => $filteredParsedJson->count() . ' records have been processed.']);
        }

        if ($source->getHeader('Content-Type')[0] === "text/csv") {
            $parsedCsv = Parser::parse($source->body(), new ParseCsv);
    
            if (!$parsedCsv) {
                return back()->withErrors(['link' => 'An error occured.']);
            }
    
            // WIP
            return back()->withErrors(['link' => 'An error occured.']);
        }

        return back()->withErrors(['link' => 'Not valid type.']);
    }

    /**
     * Reading the content from File (Content will be read as a JSON string, will not be decoded)
     *
     * Parse it through via @property Parser $parser (Read content will be parsed into a LazyCollection for the performance)
     * Matching records will be thrown to the queue for execution (Avoiding any halt while processing.)
     *
     * @param Request $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function file(Request $request)
    {
        $validated = $request->validate([
            'file' => 'file'
        ]);

        if ($validated["file"]->getClientMimeType() === "application/json") {
            $parsedJson = Parser::parse($validated["file"]->getContent(), new ParseJson);

            if (!$parsedJson) {
                return back()->withErrors(['file' => 'An error occured.']);
            }

            $filteredParsedJson = $this->filterCreditCardNumberHasConsecutiveNumber($this->filterAge($parsedJson));

            RecordsCollection::dispatch($filteredParsedJson->all())->onQueue('medium');

            return back()->with(['message' => $filteredParsedJson->count() . ' records have been processed.']);
        }

        if ($validated["file"]->getClientMimeType() === "text/csv") {
            $parsedCsv = Parser::parse($source->body(), new ParseCsv);
            
            if (!$parsedCsv) {
                return back()->withErrors(['file' => 'An error occured.']);
            }
            
            // WIP
            return back()->withErrors(['file' => 'An error occured.']);
        }

        return back()->withErrors(['file' => 'An error occured.']);
    }

    /**
     * Filter LazyCollection for ages between 18 and 65 or age is not set
     *
     * @param LazyCollection $source
     * @return LazyCollection
     */
    protected function filterAge(LazyCollection $source)
    {
        return $source->filter(function ($record) {
            if (!isset($record["date_of_birth"]) && $record["date_of_birth"] !== null) {
                return false;
            }
    
            if ($record["date_of_birth"] === null) {
                return true;
            }
    
            try {
                $age = Carbon::parse($record["date_of_birth"])->age;
            } catch (\Exception $e) {
                return false;
            }
    
            if (18 <= $age && $age <= 65) {
                return true;
            }
        });
    }

    /**
     * Filter for credit card number has consecutive number
     *
     * @param LazyCollection $source
     * @return LazyCollection
     */
    public function filterCreditCardNumberHasConsecutiveNumber(LazyCollection $source)
    {
        return $source->filter(function ($record) {
            $patterns = ["999", "888", "777", "666", "555", "444", "333", "222", "111", "000"];
            foreach ($patterns as $pattern) {
                if (str_contains($record["credit_card"]["number"], $pattern)) {
                    return true;
                }
            }
        });
    }
}
