<?php

namespace App\Livewire\Admin;

use App\Models\Expense;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ExpenseIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $categoryFilter = '';
    public string $propertyFilter = '';
    public string $monthFilter = '';

    // Create/Edit modal
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Form fields
    public string $title = '';
    public string $description = '';
    public string $amount = '';
    public string $expense_date = '';
    public string $category = 'other';
    public $property_id = '';
    public $receipt_image;

    // Detail modal
    public bool $showDetailModal = false;
    public ?Expense $selectedExpense = null;

    // Delete confirmation
    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    public function mount()
    {
        if (!Auth::user()->can('view-expenses')) {
            abort(403);
        }
        $this->monthFilter = now()->format('Y-m');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedPropertyFilter()
    {
        $this->resetPage();
    }

    public function updatedMonthFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['title', 'description', 'amount', 'category', 'property_id', 'receipt_image', 'editingId', 'isEditing']);
        $this->expense_date = now()->format('Y-m-d');
        $this->category = 'other';
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $expense = Expense::findOrFail($id);
        $this->editingId = $expense->id;
        $this->isEditing = true;
        $this->title = $expense->title;
        $this->description = $expense->description ?? '';
        $this->amount = (string) $expense->amount;
        $this->expense_date = $expense->expense_date->format('Y-m-d');
        $this->category = $expense->category;
        $this->property_id = $expense->property_id ?? '';
        $this->receipt_image = null;
        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'category' => 'required|in:maintenance,utility,cleaning,supplies,salary,tax,insurance,other',
            'property_id' => 'nullable|exists:properties,id',
        ];

        if ($this->receipt_image) {
            $rules['receipt_image'] = 'mimes:jpeg,jpg,png,gif,webp,avif|max:8192';
        }

        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date,
            'category' => $this->category,
            'property_id' => $this->property_id ?: null,
        ];

        if ($this->receipt_image) {
            $data['receipt_image'] = $this->receipt_image->store('expenses/receipts', 'public');
        }

        if ($this->isEditing && $this->editingId) {
            $expense = Expense::findOrFail($this->editingId);
            $expense->update($data);
            session()->flash('message', 'Pengeluaran berhasil diperbarui');
        } else {
            $data['created_by'] = Auth::id();
            Expense::create($data);
            session()->flash('message', 'Pengeluaran berhasil ditambahkan');
        }

        $this->showModal = false;
        $this->reset(['title', 'description', 'amount', 'expense_date', 'category', 'property_id', 'receipt_image', 'editingId', 'isEditing']);
    }

    public function showDetail(int $id)
    {
        $this->selectedExpense = Expense::with(['property', 'creator'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deleteId) {
            Expense::findOrFail($this->deleteId)->delete();
            session()->flash('message', 'Pengeluaran berhasil dihapus');
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function getSummary(): array
    {
        $query = Expense::query();

        if ($this->monthFilter) {
            $query->whereYear('expense_date', substr($this->monthFilter, 0, 4))
                ->whereMonth('expense_date', substr($this->monthFilter, 5, 2));
        }

        if ($this->propertyFilter) {
            $query->where('property_id', $this->propertyFilter);
        }

        return [
            'total' => (clone $query)->sum('amount'),
            'count' => (clone $query)->count(),
            'by_category' => (clone $query)
                ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('category')
                ->orderByDesc('total')
                ->get(),
        ];
    }

    public function render()
    {
        $query = Expense::with(['property', 'creator']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->propertyFilter) {
            $query->where('property_id', $this->propertyFilter);
        }

        if ($this->monthFilter) {
            $query->whereYear('expense_date', substr($this->monthFilter, 0, 4))
                ->whereMonth('expense_date', substr($this->monthFilter, 5, 2));
        }

        return view('livewire.admin.expense-index', [
            'expenses' => $query->orderByDesc('expense_date')->paginate(10),
            'properties' => Property::orderBy('name')->get(),
            'summary' => $this->getSummary(),
        ]);
    }
}
