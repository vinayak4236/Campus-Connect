// Admin Panel JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Image preview for file inputs
    const imageInputs = document.querySelectorAll('.image-input');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.querySelector(this.dataset.preview);
            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.style.backgroundImage = `url(${e.target.result})`;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Add schedule item for events
    const addScheduleBtn = document.getElementById('addScheduleItem');
    if (addScheduleBtn) {
        addScheduleBtn.addEventListener('click', function() {
            const scheduleContainer = document.getElementById('scheduleItems');
            const itemCount = scheduleContainer.querySelectorAll('.schedule-item').length;
            
            const newItem = document.createElement('div');
            newItem.className = 'schedule-item';
            newItem.innerHTML = `
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="schedule_time_${itemCount}" class="form-label">Time</label>
                        <input type="text" class="form-control" id="schedule_time_${itemCount}" name="schedule_time[]" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="schedule_activity_${itemCount}" class="form-label">Activity</label>
                        <input type="text" class="form-control" id="schedule_activity_${itemCount}" name="schedule_activity[]" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="schedule_location_${itemCount}" class="form-label">Location</label>
                        <input type="text" class="form-control" id="schedule_location_${itemCount}" name="schedule_location[]" required>
                    </div>
                </div>
                <span class="remove-item" title="Remove Item"><i class="fas fa-times"></i></span>
            `;
            
            scheduleContainer.appendChild(newItem);
            
            // Add event listener to remove button
            const removeBtn = newItem.querySelector('.remove-item');
            removeBtn.addEventListener('click', function() {
                scheduleContainer.removeChild(newItem);
            });
        });
    }

    // Add related event for events
    const addRelatedBtn = document.getElementById('addRelatedEvent');
    if (addRelatedBtn) {
        addRelatedBtn.addEventListener('click', function() {
            const relatedContainer = document.getElementById('relatedEvents');
            const itemCount = relatedContainer.querySelectorAll('.related-event-item').length;
            
            const newItem = document.createElement('div');
            newItem.className = 'related-event-item mb-3';
            newItem.innerHTML = `
                <div class="input-group">
                    <select class="form-select" name="related_events[]" required>
                        <option value="">Select Related Event</option>
                        ${document.getElementById('event_related_template').innerHTML}
                    </select>
                    <button class="btn btn-outline-danger remove-related" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            relatedContainer.appendChild(newItem);
            
            // Add event listener to remove button
            const removeBtn = newItem.querySelector('.remove-related');
            removeBtn.addEventListener('click', function() {
                relatedContainer.removeChild(newItem);
            });
        });
    }

    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});