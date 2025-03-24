def quicksort(arr, low, high):
    if low < high:
        pivot_index = partition(arr, low, high)
        quicksort(arr, low, pivot_index - 1)  # Sort left part
        quicksort(arr, pivot_index + 1, high)  # Sort right part

def partition(arr, low, high):
    pivot = arr[high]  # Choosing last element as pivot
    i = low - 1  # Pointer for smaller elements
    for j in range(low, high):
        if arr[j] < pivot:
            i += 1
            arr[i], arr[j] = arr[j], arr[i]  # Swap if element is smaller than pivot
    arr[i + 1], arr[high] = arr[high], arr[i + 1]  # Swap pivot to correct position
    return i + 1  # Return pivot index

# Example Usage
arr = [9, 3, 1, 5, 13, 7]
quicksort(arr, 0, len(arr) - 1)
print(arr)  # Output: [1, 3, 5, 7, 9, 13]
