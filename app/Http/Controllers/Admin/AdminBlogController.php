<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogRequest;
use App\Http\Requests\Admin\UpdateBlogRequest;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBlogController extends Controller
{
    /**
     * ブログ一覧画面
     */
    public function index()
    {
        $blogs = Blog::latest('updated_at')->simplepaginate(10);
        return view('admin.blogs.index', ['blogs' => $blogs]);
    }

    /**
     * ブログ投稿画面
     */
    public function create()
    {
        return view('admin.blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request)
    {
        $savedImagePath = $request->file('image')->store('blogs', 'public');
        $blog = new Blog($request->validated());
        $blog->image = $savedImagePath;
        $blog->save();

        return to_route('admin.blogs.index')->with('success', 'ブログを投稿しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * 指定したIDのブログ編集画面
     */
    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', ['blog' => $blog]);
    }

    /**
     * ブログの更新処理
     */
    public function update(UpdateBlogRequest $request, string $id)
    {
        $blog = Blog::findOrFail($id);
        $updateData = $request->validated();

        // 画像を変更する場合
        if($request->hasFile('image'))
        {
            //変更前の画像を削除
            Storage::disk('public')->delete($blog->image);
            //変更後の画像をアップロード、ほぉんパスを更新対象にセット
            $updateData['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog->update($updateData);

        return to_route('admin.blogs.index')->with('success', 'ブログを更新しました');
    }

    /**
     *
     * 削除処理
     */
    public function destroy(string $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();
        Storage::disk('public')->delete($blog->image);

        return to_route('admin.blogs.index')->with('success', 'ブログを削除しました');
    }
}
