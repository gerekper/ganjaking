using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;

namespace AspnetCoreFull.Pages;

public class IndexModel : PageModel
{
  private readonly ILogger<IndexModel> _logger;

  public IndexModel(ILogger<IndexModel> logger)
  {
    _logger = logger;
  }

  public void OnGet()
  {
    // On Index page load, redirects to dashboard
    Response.Redirect("Dashboards/Analytics");
  }
}
