<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * CRUD generik berbasis konfigurasi. Setiap resource mendefinisikan cfg():
 * model, route, title, label, folder, columns, fields, search, order, slug_from.
 */
abstract class CrudController extends Controller
{
    abstract protected function cfg(): array;

    protected function extra(Model $item): array
    {
        return [];
    }

    protected function extraRules(?Model $item): array
    {
        return [];
    }

    protected function beforeSave(Model $item, Request $r): void
    {
    }

    protected function afterSave(Model $item, Request $r): void
    {
    }

    protected function beforeDelete(Model $item): ?string
    {
        return null;
    }

    public function index(Request $r)
    {
        $c = $this->cfg();
        $q = $c['model']::query();
        if ($s = trim((string) $r->get('q'))) {
            $q->where(function ($w) use ($c, $s) {
                foreach ($c['search'] as $col) {
                    $w->orWhere($col, 'like', '%' . $s . '%');
                }
            });
        }
        if (!empty($c['has_status']) && in_array($r->get('status'), ['published', 'draft'], true)) {
            $q->where('status', $r->get('status'));
        }
        $order = $c['order'] ?? ['id', 'desc'];
        $items = $q->orderBy($order[0], $order[1])->paginate(10)->withQueryString();

        return view('admin.crud.index', compact('c', 'items'));
    }

    public function create()
    {
        $item = new ($this->cfg()['model']);

        return view('admin.crud.form', ['c' => $this->cfg(), 'item' => $item, 'extra' => $this->extra($item)]);
    }

    public function store(Request $r)
    {
        $item = $this->persist($r, new ($this->cfg()['model']));
        $this->log('create', $item, 'Membuat');

        return redirect()->route('admin.' . $this->cfg()['route'] . '.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $item = $this->cfg()['model']::findOrFail($id);

        return view('admin.crud.form', ['c' => $this->cfg(), 'item' => $item, 'extra' => $this->extra($item)]);
    }

    public function update(Request $r, string $id)
    {
        $item = $this->persist($r, $this->cfg()['model']::findOrFail($id));
        $this->log('update', $item, 'Mengubah');

        return redirect()->route('admin.' . $this->cfg()['route'] . '.index')->with('success', 'Perubahan berhasil disimpan.');
    }

    public function destroy(string $id)
    {
        $c = $this->cfg();
        $item = $c['model']::findOrFail($id);
        if ($msg = $this->beforeDelete($item)) {
            return back()->with('error', $msg);
        }
        foreach ($c['fields'] as $f) {
            if ($f['type'] === 'image') {
                Media::delete($item->{$f['name']});
            }
        }
        $this->log('delete', $item, 'Menghapus');
        $item->delete();

        return redirect()->route('admin.' . $c['route'] . '.index')->with('success', 'Data berhasil dihapus.');
    }

    protected function rules(Model $item): array
    {
        $c = $this->cfg();
        $rules = [];
        foreach ($c['fields'] as $f) {
            $r = $f['rules'] ?? 'nullable';
            if (is_callable($r)) {
                $r = $r($item);
            }
            if ($f['type'] === 'image') {
                $r = ($f['required_on_create'] ?? false) && !$item->exists
                    ? 'required|image|mimes:jpg,jpeg,png,webp|max:3072'
                    : 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072';
            }
            if ($f['type'] === 'slug') {
                $r = ['nullable', 'alpha_dash', 'max:150', Rule::unique((new $c['model'])->getTable(), 'slug')->ignore($item->id)];
            }
            $rules[$f['name']] = $r;
        }

        return array_merge($rules, $this->extraRules($item));
    }

    protected function persist(Request $r, Model $item): Model
    {
        $c = $this->cfg();
        $data = $r->validate($this->rules($item));
        $this->beforeSave($item, $r);

        foreach ($c['fields'] as $f) {
            $n = $f['name'];
            switch ($f['type']) {
                case 'image':
                    if ($r->hasFile($n)) {
                        Media::delete($item->$n);
                        $item->$n = $r->file($n)->store($c['folder'], 'public');
                    }
                    break;
                case 'slug':
                    break;
                case 'checkbox':
                    $item->$n = $r->boolean($n);
                    break;
                case 'password':
                    if (!empty($data[$n])) {
                        $item->$n = $data[$n]; // di-hash oleh cast 'hashed'
                    }
                    break;
                default:
                    $item->$n = $data[$n] ?? ($f['default'] ?? null);
            }
        }

        if (!empty($c['slug_from'])) {
            $slug = $data['slug'] ?? null;
            if (!$slug && !$item->slug) {
                $slug = $this->uniqueSlug(Str::slug($data[$c['slug_from']]) ?: 'item', $item);
            }
            if ($slug) {
                $item->slug = $slug;
            }
        }

        $item->save();
        $this->afterSave($item, $r);

        return $item;
    }

    protected function uniqueSlug(string $base, Model $item): string
    {
        $slug = $base;
        $i = 2;
        while ($item::where('slug', $slug)->where('id', '!=', $item->id ?? 0)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    protected function log(string $action, Model $item, string $verb): void
    {
        $c = $this->cfg();
        ActivityLog::record($action, class_basename($item), $item->id, $verb . ' ' . Str::lower($c['title']) . ': ' . ($item->{$c['label']} ?? '#' . $item->id));
    }
}
