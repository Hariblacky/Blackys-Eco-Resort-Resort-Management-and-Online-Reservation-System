    </div>
  </div>
</div>
<script>
  document.getElementById('menuToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
  });
  // confirm before any destructive delete action
  document.querySelectorAll('form.confirm-delete').forEach(f => {
    f.addEventListener('submit', e => {
      if (!confirm('Are you sure you want to delete this? This cannot be undone.')) e.preventDefault();
    });
  });
</script>
</body>
</html>
