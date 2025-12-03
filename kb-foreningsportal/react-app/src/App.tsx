import React from 'react';
import AdminLayout from './components/layout/AdminLayout';
import OrgLayout from './components/layout/OrgLayout';
import AdminDashboardPage from './pages/admin/AdminDashboardPage';
import AdminOrgsPage from './pages/admin/AdminOrgsPage';
import AdminOrgDetailPage from './pages/admin/AdminOrgDetailPage';
import AdminAdminsPage from './pages/admin/AdminAdminsPage';
import AdminReportsPage from './pages/admin/AdminReportsPage';
import AdminSettingsPage from './pages/admin/AdminSettingsPage';
import OrgDashboardPage from './pages/org/OrgDashboardPage';
import OrgMembersPage from './pages/org/OrgMembersPage';
import OrgMaterialsPage from './pages/org/OrgMaterialsPage';
import OrgCampaignPage from './pages/org/OrgCampaignPage';

export type PortalMode = 'admin' | 'org';

type AppProps = {
  mode: PortalMode;
};

const App: React.FC<AppProps> = ({ mode }) => {
  if (mode === 'admin') {
    return (
      <AdminLayout
        pages={[
          { id: 'admin-dashboard', title: 'Dashboard', element: <AdminDashboardPage /> },
          { id: 'admin-orgs', title: 'Föreningar', element: <AdminOrgsPage /> },
          { id: 'admin-org-detail', title: 'Föreningsdetaljer', element: <AdminOrgDetailPage /> },
          { id: 'admin-admins', title: 'Admins', element: <AdminAdminsPage /> },
          { id: 'admin-reports', title: 'Rapporter', element: <AdminReportsPage /> },
          { id: 'admin-settings', title: 'Inställningar', element: <AdminSettingsPage /> },
        ]}
      />
    );
  }

  return (
    <OrgLayout
      pages={[
        { id: 'org-dashboard', title: 'Dashboard', element: <OrgDashboardPage /> },
        { id: 'org-members', title: 'Medlemmar', element: <OrgMembersPage /> },
        { id: 'org-materials', title: 'Material', element: <OrgMaterialsPage /> },
        { id: 'org-campaign', title: 'Kampanj', element: <OrgCampaignPage /> },
      ]}
    />
  );
};

export default App;
