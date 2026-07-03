<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'display_name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        $genres = ['Fantasy', 'Science Fiction', 'Mystery', 'Thriller', 'Romance', 'Historical Fiction', 'Non-Fiction', 'Biography', 'Horror', 'Poetry'];
        foreach ($genres as $genreName) {
            \App\Models\Genre::create(['name' => $genreName]);
        }
        
        $authors = \App\Models\Author::factory(15)->create();
        $genres = \App\Models\Genre::all();
        
        \App\Models\Book::factory(30)->create()->each(function ($book) use ($authors, $genres) {
            // attach 1 to 3 random authors
            $book->authors()->attach($authors->random(rand(1, 3))->pluck('id')->toArray());
            // attach 1 to 2 random genres
            $book->genres()->attach($genres->random(rand(1, 2))->pluck('id')->toArray());
        });

        $sampleUsers = User::factory(5)->create();
        $allBooks = \App\Models\Book::all();
        
        foreach ($sampleUsers as $u) {
            $readShelf = \App\Models\Shelf::create(['user_id' => $u->id, 'name' => 'Read', 'is_default' => true]);
            $currentlyReadingShelf = \App\Models\Shelf::create(['user_id' => $u->id, 'name' => 'Currently Reading', 'is_default' => true]);
            $wantToReadShelf = \App\Models\Shelf::create(['user_id' => $u->id, 'name' => 'Want to Read', 'is_default' => true]);
            $didNotFinishShelf = \App\Models\Shelf::create(['user_id' => $u->id, 'name' => 'Did Not Finish', 'is_default' => true]);
            
            $booksForUser = $allBooks->random(5);
            foreach ($booksForUser as $idx => $b) {
                if ($idx < 3) {
                    \App\Models\ShelfBook::create(['shelf_id' => $readShelf->id, 'book_id' => $b->id, 'user_id' => $u->id]);
                    \App\Models\Rating::create(['user_id' => $u->id, 'book_id' => $b->id, 'stars' => rand(3, 5)]);
                } elseif ($idx == 3) {
                    \App\Models\ShelfBook::create(['shelf_id' => $currentlyReadingShelf->id, 'book_id' => $b->id, 'user_id' => $u->id]);
                } else {
                    \App\Models\ShelfBook::create(['shelf_id' => $wantToReadShelf->id, 'book_id' => $b->id, 'user_id' => $u->id]);
                }
            }
        }
    }
}
