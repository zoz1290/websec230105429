<?php
// filepath: c:\Users\ziadm\OneDrive\Desktop\websec-main\WebSecService\database\migrations\2025_03_24_000001_create_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        // // Schema::create('products', function (Blueprint $table) {
        // //     $table->id();
        // //     $table->string('name');
        // //     $table->decimal('price', 8, 2);
        // //     $table->integer('quantity')->default(0);
        // //     $table->timestamps();
        // });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}