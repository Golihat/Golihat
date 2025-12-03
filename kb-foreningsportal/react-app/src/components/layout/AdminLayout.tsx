import React, { useMemo, useState } from 'react';
import NavBar from './NavBar';
import { PageConfig } from '../../types';
import Card from '../ui/Card';

type AdminLayoutProps = {
  pages: PageConfig[];
};

const AdminLayout: React.FC<AdminLayoutProps> = ({ pages }) => {
  const [activePageId, setActivePageId] = useState(pages[0]?.id ?? '');

  const ActivePage = useMemo(() => pages.find((p) => p.id === activePageId)?.element, [activePageId, pages]);

  return (
    <div className="kb-layout">
      <NavBar
        title="KB Adminportal"
        items={pages.map((page) => ({ id: page.id, label: page.title }))}
        activeId={activePageId}
        onNavigate={setActivePageId}
      />
      <main className="kb-content">
        <Card>
          {ActivePage ?? <p>Ingen sida vald.</p>}
        </Card>
      </main>
    </div>
  );
};

export default AdminLayout;
