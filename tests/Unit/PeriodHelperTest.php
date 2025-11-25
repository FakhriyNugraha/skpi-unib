<?php

namespace Tests\Unit;

use App\Helpers\PeriodHelper;
use Carbon\Carbon;
use Tests\TestCase;

class PeriodHelperTest extends TestCase
{
    public function test_get_periode_from_date_correctly_maps_new_schedule()
    {
        // Test new schedule periods
        // October-December 2025 should be period 112
        $this->assertEquals(112, PeriodHelper::getPeriodeFromDate('2025-10-01'));
        $this->assertEquals(112, PeriodHelper::getPeriodeFromDate('2025-11-15'));
        $this->assertEquals(112, PeriodHelper::getPeriodeFromDate('2025-12-31'));
        
        // January-March 2026 should be period 113
        $this->assertEquals(113, PeriodHelper::getPeriodeFromDate('2026-01-01'));
        $this->assertEquals(113, PeriodHelper::getPeriodeFromDate('2026-02-15'));
        $this->assertEquals(113, PeriodHelper::getPeriodeFromDate('2026-03-31'));
        
        // April-June 2026 should be period 114
        $this->assertEquals(114, PeriodHelper::getPeriodeFromDate('2026-04-01'));
        $this->assertEquals(114, PeriodHelper::getPeriodeFromDate('2026-05-15'));
        $this->assertEquals(114, PeriodHelper::getPeriodeFromDate('2026-06-30'));
        
        // July-September 2026 should be period 115
        $this->assertEquals(115, PeriodHelper::getPeriodeFromDate('2026-07-01'));
        $this->assertEquals(115, PeriodHelper::getPeriodeFromDate('2026-08-15'));
        $this->assertEquals(115, PeriodHelper::getPeriodeFromDate('2026-09-30'));
        
        // October-December 2026 should be period 116
        $this->assertEquals(116, PeriodHelper::getPeriodeFromDate('2026-10-01'));
        $this->assertEquals(116, PeriodHelper::getPeriodeFromDate('2026-11-15'));
        $this->assertEquals(116, PeriodHelper::getPeriodeFromDate('2026-12-31'));
    }

    public function test_get_period_range_returns_correct_dates()
    {
        // Test October-December 2025 (Period 112)
        $range112 = PeriodHelper::getPeriodRange(112);
        $this->assertEquals('2025-10-01 00:00:00', $range112['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('2025-12-31 23:59:59', $range112['end']->format('Y-m-d H:i:s'));
        $this->assertEquals('Oktober-Desember 2025', $range112['title']);
        
        // Test January-March 2026 (Period 113)
        $range113 = PeriodHelper::getPeriodRange(113);
        $this->assertEquals('2026-01-01 00:00:00', $range113['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('2026-03-31 23:59:59', $range113['end']->format('Y-m-d H:i:s'));
        $this->assertEquals('Januari-Maret 2026', $range113['title']);
        
        // Test April-June 2026 (Period 114)
        $range114 = PeriodHelper::getPeriodRange(114);
        $this->assertEquals('2026-04-01 00:00:00', $range114['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('2026-06-30 23:59:59', $range114['end']->format('Y-m-d H:i:s'));
        $this->assertEquals('April-Juni 2026', $range114['title']);
        
        // Test July-September 2026 (Period 115)
        $range115 = PeriodHelper::getPeriodRange(115);
        $this->assertEquals('2026-07-01 00:00:00', $range115['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('2026-09-30 23:59:59', $range115['end']->format('Y-m-d H:i:s'));
        $this->assertEquals('Juli-September 2026', $range115['title']);
    }
    
    public function test_get_current_period()
    {
        // Test with current date (should return appropriate period)
        $currentPeriod = PeriodHelper::getCurrentPeriod();
        $this->assertIsInt($currentPeriod);
        $this->assertGreaterThanOrEqual(112, $currentPeriod);
    }
}