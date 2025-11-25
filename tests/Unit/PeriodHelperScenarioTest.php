<?php

namespace Tests\Unit;

use App\Helpers\PeriodHelper;
use Carbon\Carbon;
use Tests\TestCase;

class PeriodHelperScenarioTest extends TestCase
{
    public function test_period_sequence_for_2025_2026()
    {
        // Test specific dates to ensure correct sequence according to requirements
        // Periode 112: Oktober-Desember 2025
        $this->assertEquals(112, PeriodHelper::getPeriodeFromDate('2025-10-01'));
        $this->assertEquals(112, PeriodHelper::getPeriodeFromDate('2025-11-15'));
        $this->assertEquals(112, PeriodHelper::getPeriodeFromDate('2025-12-31'));
        
        // Periode 113: Januari-Maret 2026
        $this->assertEquals(113, PeriodHelper::getPeriodeFromDate('2026-01-01'));
        $this->assertEquals(113, PeriodHelper::getPeriodeFromDate('2026-02-15'));
        $this->assertEquals(113, PeriodHelper::getPeriodeFromDate('2026-03-31'));
        
        // Periode 114: April-Juni 2026
        $this->assertEquals(114, PeriodHelper::getPeriodeFromDate('2026-04-01'));
        $this->assertEquals(114, PeriodHelper::getPeriodeFromDate('2026-05-15'));
        $this->assertEquals(114, PeriodHelper::getPeriodeFromDate('2026-06-30'));
        
        // Periode 115: Juli-September 2026
        $this->assertEquals(115, PeriodHelper::getPeriodeFromDate('2026-07-01'));
        $this->assertEquals(115, PeriodHelper::getPeriodeFromDate('2026-08-15'));
        $this->assertEquals(115, PeriodHelper::getPeriodeFromDate('2026-09-30'));
        
        // Periode 116: Oktober-Desember 2026
        $this->assertEquals(116, PeriodHelper::getPeriodeFromDate('2026-10-01'));
        $this->assertEquals(116, PeriodHelper::getPeriodeFromDate('2026-11-15'));
        $this->assertEquals(116, PeriodHelper::getPeriodeFromDate('2026-12-31'));
    }

    public function test_period_ranges_for_2025_2026()
    {
        // Test that ranges are correct for all 4 periods in a year
        $range112 = PeriodHelper::getPeriodRange(112);
        $this->assertEquals('Oktober-Desember 2025', $range112['title']);
        
        $range113 = PeriodHelper::getPeriodRange(113);
        $this->assertEquals('Januari-Maret 2026', $range113['title']);
        
        $range114 = PeriodHelper::getPeriodRange(114);
        $this->assertEquals('April-Juni 2026', $range114['title']);
        
        $range115 = PeriodHelper::getPeriodRange(115);
        $this->assertEquals('Juli-September 2026', $range115['title']);
        
        $range116 = PeriodHelper::getPeriodRange(116);
        $this->assertEquals('Oktober-Desember 2026', $range116['title']);
    }
    
    public function test_period_across_multiple_years()
    {
        // Test year 2027 periods
        // Periode 117: Januari-Maret 2027
        $this->assertEquals(117, PeriodHelper::getPeriodeFromDate('2027-02-01'));
        
        // Periode 118: April-Juni 2027
        $this->assertEquals(118, PeriodHelper::getPeriodeFromDate('2027-05-01'));
        
        // Periode 119: Juli-September 2027
        $this->assertEquals(119, PeriodHelper::getPeriodeFromDate('2027-08-01'));
        
        // Periode 120: Oktober-Desember 2027
        $this->assertEquals(120, PeriodHelper::getPeriodeFromDate('2027-11-01'));
    }
}