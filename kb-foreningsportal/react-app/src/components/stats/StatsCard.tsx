import React from 'react';
import Card from '../ui/Card';
import { StatDatum } from '../../types';

type StatsCardProps = {
  title: string;
  stats: StatDatum[];
};

const StatsCard: React.FC<StatsCardProps> = ({ title, stats }) => (
  <Card title={title}>
    <div className="kb-stats">
      {stats.map((stat) => (
        <div key={stat.label} className="kb-stats__item">
          <div className="kb-stats__label">{stat.label}</div>
          <div className="kb-stats__value">{stat.value}</div>
          {stat.delta && <div className="kb-stats__delta">{stat.delta}</div>}
        </div>
      ))}
    </div>
  </Card>
);

export default StatsCard;
