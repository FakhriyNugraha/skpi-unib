<?php

namespace App\Helpers;

use Carbon\Carbon;

class PeriodHelper
{
    /**
     * Get the period number based on a given date.
     * Periods follow this pattern:
     * - Period 112: September-December 2025
     * - Period 113: January-April 2026
     * - Period 114: May-August 2026
     * - Period 115: September-December 2026
     * - Period 116: January-April 2027
     * This repeats with a 4-period cycle.
     *
     * @param string|Carbon $date The date to determine the period for
     * @param int $startingPeriod The starting base period (default 112)
     * @param int $startingYear The starting year for the first period (default 2025)
     * @return int The period number
     */
    public static function getPeriodeFromDate($date, $startingPeriod = 112, $startingYear = 2025)
    {
        $carbonDate = is_string($date) ? Carbon::parse($date) : $date;

        $month = $carbonDate->month;
        $year = $carbonDate->year;

        // Determine which academic year the date belongs to
        // Academic year: Sep-Dec belongs to the current calendar year, Jan-Aug belongs to the following academic year
        if ($month >= 9 && $month <= 12) {
            // September-December: belongs to the current academic year
            $academicYear = $year;
        } else {
            // January-August: belongs to the previous academic year
            $academicYear = $year - 1;
        }

        // Determine period within the academic year based on month
        if ($month >= 9 && $month <= 12) {
            // September-December: First period
            $periodInAcademicYear = 0; // 0-indexed
        } elseif ($month >= 1 && $month <= 4) {
            // January-April: Second period
            $periodInAcademicYear = 1; // 0-indexed
        } elseif ($month >= 5 && $month <= 8) {
            // May-August: Third period
            $periodInAcademicYear = 2; // 0-indexed
        } else {
            // This should not happen
            $periodInAcademicYear = 0;
        }

        // Calculate how many academic years have passed since the starting year
        $yearDiff = $academicYear - $startingYear;

        // Calculate the total periods from the start
        $totalPeriodsFromStart = ($yearDiff * 4) + $periodInAcademicYear;

        return $startingPeriod + $totalPeriodsFromStart;
    }

    /**
     * Get the period range (start and end) for a given period number
     *
     * @param int $periodNumber
     * @param int $startingPeriod
     * @param int $startingYear
     * @return array ['start' => Carbon, 'end' => Carbon, 'title' => string]
     */
    public static function getPeriodRange($periodNumber, $startingPeriod = 112, $startingYear = 2025)
    {
        // Calculate how many periods have passed since the starting period
        $periodOffset = $periodNumber - $startingPeriod;

        // Calculate the academic year based on periods passed (4 periods per academic year)
        $yearOffset = intdiv($periodOffset, 4);
        $periodInCycle = $periodOffset % 4;

        // Adjust for negative modulo
        if ($periodInCycle < 0) {
            $yearOffset--;
            $periodInCycle += 4;
        }

        $academicYear = $startingYear + $yearOffset;

        // Determine date range based on which period in the academic year this is
        switch ($periodInCycle) {
            case 0: // First period: September-December of academic year
                $start = Carbon::create($academicYear, 9, 1, 0, 0, 0);
                $end = Carbon::create($academicYear, 12, 31, 23, 59, 59);
                $title = "September-Desember " . $academicYear;
                break;
            case 1: // Second period: January-April of following calendar year
                $start = Carbon::create($academicYear + 1, 1, 1, 0, 0, 0);
                $end = Carbon::create($academicYear + 1, 4, 30, 23, 59, 59);
                $title = "Januari-April " . ($academicYear + 1);
                break;
            case 2: // Third period: May-August of following calendar year
                $start = Carbon::create($academicYear + 1, 5, 1, 0, 0, 0);
                $end = Carbon::create($academicYear + 1, 8, 31, 23, 59, 59);
                $title = "Mei-Agustus " . ($academicYear + 1);
                break;
            default: // Fourth period: September-December of following academic year
                $start = Carbon::create($academicYear + 1, 9, 1, 0, 0, 0);
                $end = Carbon::create($academicYear + 1, 12, 31, 23, 59, 59);
                $title = "September-Desember " . ($academicYear + 1);
                break;
        }

        return [
            'start' => $start,
            'end' => $end,
            'title' => $title
        ];
    }

    /**
     * Get the current period based on today's date
     *
     * @param int $startingPeriod
     * @param int $startingYear
     * @return int
     */
    public static function getCurrentPeriod($startingPeriod = 112, $startingYear = 2025)
    {
        return self::getPeriodeFromDate(now(), $startingPeriod, $startingYear);
    }

    /**
     * Get a list of periods within a range
     *
     * @param int $startPeriod
     * @param int $endPeriod
     * @param int $startingPeriod
     * @param int $startingYear
     * @return array
     */
    public static function getPeriodList($startPeriod = null, $endPeriod = null, $startingPeriod = 112, $startingYear = 2025)
    {
        $currentPeriod = self::getCurrentPeriod($startingPeriod, $startingYear);

        if (!$startPeriod) {
            $startPeriod = $startingPeriod;
        }

        if (!$endPeriod) {
            $endPeriod = $currentPeriod + 4; // Go 4 periods ahead
        }

        $periods = [];
        for ($i = $startPeriod; $i <= $endPeriod; $i++) {
            $range = self::getPeriodRange($i, $startingPeriod, $startingYear);
            $periods[] = [
                'number' => $i,
                'title' => $range['title'],
                'start' => $range['start'],
                'end' => $range['end']
            ];
        }

        return $periods;
    }
}