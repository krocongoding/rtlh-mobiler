<?php
namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class AuditLogger { public static function log(?User $user,string $action,string $type,?int $id,?array $old=null,?array $new=null):void{DB::table('audit_logs')->insert(['user_id'=>$user?->id,'action'=>$action,'auditable_type'=>$type,'auditable_id'=>$id,'old_values'=>$old?json_encode($old):null,'new_values'=>$new?json_encode($new):null,'ip_address'=>request()->ip(),'user_agent'=>request()->userAgent(),'created_at'=>now(),'updated_at'=>now()]);} }
