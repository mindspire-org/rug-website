<?php

namespace Database\Seeders;

use App\Models\SampleRequest;
use App\Models\TradeProject;
use App\Models\TradeQuote;
use App\Models\User;
use Illuminate\Database\Seeder;

class TradeDataSeeder extends Seeder
{
    public function run(): void
    {
        $tradeUser = User::where('role', User::ROLE_TRADE)->first();
        if (!$tradeUser) {
            $tradeUser = User::where('email', 'customer@costikyancustomcarpet.com')->first();
        }
        if (!$tradeUser) return;

        $tradeUser->update([
            'company_name'   => 'Studio Interiors',
            'trade_discount' => 25,
        ]);

        $projects = [
            ['name'=>'Tabriz Heritage Collection','client_name'=>'Sarah Mitchell','room'=>'Living Room',  'status'=>'active',    'rugs_count'=>4, 'total_value'=>18400],
            ['name'=>'Sultanabad Grand Series',   'client_name'=>'James Park',    'room'=>'Master Suite', 'status'=>'active',    'rugs_count'=>2, 'total_value'=>9200],
            ['name'=>'Agra Imperial Runner',      'client_name'=>'The Whites',    'room'=>'Multiple',     'status'=>'active',    'rugs_count'=>6, 'total_value'=>32100],
            ['name'=>'Ziegler Modern Series',     'client_name'=>'Elena Rossi',   'room'=>'Dining Room',  'status'=>'archived',  'rugs_count'=>2, 'total_value'=>5800],
            ['name'=>'Heriz Medallion Collection','client_name'=>'Robert Chen',   'room'=>'Library',      'status'=>'completed', 'rugs_count'=>3, 'total_value'=>14600],
        ];

        foreach ($projects as $p) {
            TradeProject::create(array_merge($p, ['user_id' => $tradeUser->id]));
        }

        $quotes = [
            ['quote_number'=>'Q-1024','status'=>'draft',   'items_count'=>4, 'total'=>18400],
            ['quote_number'=>'Q-1023','status'=>'sent',    'items_count'=>2, 'total'=>9200],
            ['quote_number'=>'Q-1022','status'=>'approved','items_count'=>6, 'total'=>32100],
            ['quote_number'=>'Q-1021','status'=>'expired', 'items_count'=>1, 'total'=>5800],
            ['quote_number'=>'Q-1025','status'=>'sent',    'items_count'=>3, 'total'=>14600],
        ];

        foreach ($quotes as $q) {
            TradeQuote::create(array_merge($q, ['user_id' => $tradeUser->id, 'project_id' => null]));
        }

        $samples = [
            ['rug_name'=>'Tabriz Heritage',   'color'=>'Ivory / Gold',    'status'=>'shipped',   'tracking_number'=>'1Z999AA10123456784'],
            ['rug_name'=>'Sultanabad Classic','color'=>'Midnight Blue',   'status'=>'approved',  'tracking_number'=>null],
            ['rug_name'=>'Agra Imperial',     'color'=>'Rust / Ivory',    'status'=>'delivered', 'tracking_number'=>'1Z999AA10123456782'],
            ['rug_name'=>'Oushak Revival',    'color'=>'Sage / Cream',    'status'=>'pending',   'tracking_number'=>null],
        ];

        foreach ($samples as $s) {
            SampleRequest::create(array_merge($s, ['user_id' => $tradeUser->id, 'product_id' => null]));
        }
    }
}
